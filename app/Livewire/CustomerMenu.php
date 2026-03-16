<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Table;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuOptionItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerMenu extends Component
{
    public $table;
    public $categories;

    public $showModal = false;
    public $showCartModal = false;
    public $isOrderSubmitted = false;
    public $editingCartIndex = null;

    public $selectedMenu = null;
    public $quantity = 1;
    public $selectedOptions = [];
    public $notes = '';

    public $cart = [];

    public function mount($qr_token)
    {
        $this->table = Table::where('qr_token', $qr_token)->firstOrFail();
        $this->categories = Category::with(['menus' => function($query) {
            $query->where('is_available', true)->with('options.items');
        }])->where('is_active', true)->get();

        // FIX BUG: Ambil keranjang dari session saat halaman pertama kali dimuat (berdasarkan ID meja)
        $this->cart = session()->get('customer_cart_' . $this->table->id, []);
    }

    // Fungsi bantuan untuk menyimpan keranjang ke Session
    private function saveCartToSession()
    {
        session()->put('customer_cart_' . $this->table->id, $this->cart);
    }

    public function openModal($menuId)
    {
        $this->selectedMenu = Menu::with('options.items')->find($menuId);
        $this->quantity = 1;
        $this->selectedOptions = [];
        $this->notes = '';
        $this->editingCartIndex = null;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedMenu = null;
        $this->editingCartIndex = null;
    }

    // --- FUNGSI KERANJANG ---
    public function addToCart()
    {
        // ==========================================
        // 1. VALIDASI OPSI WAJIB (REQUIRED OPTIONS)
        // ==========================================
        foreach ($this->selectedMenu->options as $option) {
            if ($option->is_required) {
                $hasSelection = false;

                // Cek apakah user sudah memilih sesuatu di opsi ini
                if (isset($this->selectedOptions[$option->id])) {
                    $val = $this->selectedOptions[$option->id];

                    if (is_array($val)) {
                        // Jika checkbox (bisa pilih banyak), pastikan minimal ada 1 yang 'true'
                        foreach ($val as $isChecked) {
                            if ($isChecked) {
                                $hasSelection = true;
                                break;
                            }
                        }
                    } else {
                        // Jika radio button (hanya 1 pilihan), pastikan tidak kosong
                        if (!empty($val)) {
                            $hasSelection = true;
                        }
                    }
                }

                // Jika wajib tapi tidak ada yang dipilih, gagalkan proses
                if (!$hasSelection) {
                    session()->flash('option_error', "Pilihan '{$option->name}' wajib diisi!");
                    return; // Hentikan fungsi di sini
                }
            }
        }

        // ==========================================
        // 2. JIKA VALIDASI LOLOS, LANJUTKAN PROSES
        // ==========================================
        $totalAdditionalPrice = 0;
        $selectedOptionsData = [];

        // Hitung harga dari opsi yang dipilih
        foreach ($this->selectedOptions as $optionId => $selectedValue) {
            if (is_array($selectedValue)) {
                // Jika checkbox (multiple choice)
                foreach ($selectedValue as $itemId => $isChecked) {
                    if ($isChecked) {
                        $item = MenuOptionItem::find($itemId);
                        if ($item) {
                            $totalAdditionalPrice += $item->additional_price;
                            $selectedOptionsData[] = [
                                'id' => $item->id,
                                'name' => $item->name,
                                'price' => $item->additional_price
                            ];
                        }
                    }
                }
            } else {
                // Jika radio button (single choice)
                $item = MenuOptionItem::find($selectedValue);
                if ($item) {
                    $totalAdditionalPrice += $item->additional_price;
                    $selectedOptionsData[] = [
                        'id' => $item->id,
                        'name' => $item->name,
                        'price' => $item->additional_price
                    ];
                }
            }
        }

        // Hitung Total
        $pricePerItem = $this->selectedMenu->base_price + $totalAdditionalPrice;
        $totalPrice = $pricePerItem * $this->quantity;

        // Siapkan array data item
        $cartItem = [
            'menu_id' => $this->selectedMenu->id,
            'name' => $this->selectedMenu->name,
            'quantity' => $this->quantity,
            'base_price' => $this->selectedMenu->base_price,
            'total_price' => $totalPrice,
            'options' => $selectedOptionsData,
            'raw_options' => $this->selectedOptions,
            'notes' => $this->notes
        ];

        if ($this->editingCartIndex !== null) {
            $this->cart[$this->editingCartIndex] = $cartItem;
            $this->editingCartIndex = null;
        } else {
            $this->cart[] = $cartItem;
        }

        $this->saveCartToSession();

        $this->closeModal();
        $this->showCartModal = true;
    }

    public function toggleCart()
    {
        $this->showCartModal = !$this->showCartModal;
    }

    public function removeCartItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);

        // FIX BUG: Simpan perubahan ke session
        $this->saveCartToSession();

        if(count($this->cart) == 0) {
            $this->showCartModal = false;
        }
    }

    public function editCartItem($index)
    {
        $item = $this->cart[$index];
        $this->selectedMenu = Menu::with('options.items')->find($item['menu_id']);
        $this->quantity = $item['quantity'] ?? 1;
        $this->selectedOptions = $item['raw_options'] ?? [];
        $this->notes = $item['notes'] ?? '';

        $this->editingCartIndex = $index;
        $this->showCartModal = false;
        $this->showModal = true;
    }

    public function submitOrder()
    {
        if (empty($this->cart)) return;

        try {
            DB::beginTransaction();

            // Hitung subtotal keranjang saat ini
            $cartSubtotal = array_sum(array_column($this->cart, 'total_price'));

            // 1. CEK APAKAH SUDAH ADA PESANAN AKTIF (BELUM DIBAYAR) DI MEJA INI
            $activeOrder = Order::where('table_id', $this->table->id)
                                ->where('payment_status', 'unpaid')
                                ->first();

            if ($activeOrder) {
                // ==========================================
                // SKENARIO A: NAMBAH PESANAN KE BILL LAMA
                // ==========================================

                // Hitung total baru (Subtotal lama + Subtotal keranjang baru)
                $newSubtotal = $activeOrder->subtotal + $cartSubtotal;
                $newTaxAmount = $newSubtotal * 0.10; // Pajak 10% dari total baru
                $newTotalAmount = $newSubtotal + $newTaxAmount;

                // Update data pesanan lama
                $activeOrder->update([
                    'subtotal'       => $newSubtotal,
                    'tax_amount'     => $newTaxAmount,
                    'total_amount'   => $newTotalAmount,
                    'kitchen_status' => 'pending', // Kembalikan status dapur ke 'pending' agar koki tahu ada tambahan
                ]);

                // Gunakan ID pesanan yang sudah ada untuk item baru
                $orderIdToUse = $activeOrder->id;

            } else {
                // ==========================================
                // SKENARIO B: BUAT PESANAN BARU (INVOICE BARU)
                // ==========================================

                $taxAmount = $cartSubtotal * 0.10;
                $totalAmount = $cartSubtotal + $taxAmount;
                $invoiceNumber = 'INV-' . strtoupper(Str::random(8));

                $newOrder = Order::create([
                    'table_id'       => $this->table->id,
                    'invoice_number' => $invoiceNumber,
                    'subtotal'       => $cartSubtotal,
                    'tax_amount'     => $taxAmount,
                    'total_amount'   => $totalAmount,
                    'kitchen_status' => 'pending',
                    'payment_status' => 'unpaid',
                ]);

                // Ubah status meja menjadi terisi
                $this->table->update(['status' => 'occupied']);

                // Gunakan ID pesanan yang baru dibuat
                $orderIdToUse = $newOrder->id;
            }

            // 2. SIMPAN SEMUA ITEM KE DATABASE (Menggunakan $orderIdToUse)
            foreach ($this->cart as $item) {
                $orderItem = OrderItem::create([
                    'order_id'    => $orderIdToUse, // <--- Dimasukkan ke Invoice yang benar
                    'menu_id'     => $item['menu_id'],
                    'quantity'    => $item['quantity'],
                    'base_price'  => $item['base_price'],
                    'total_price' => $item['total_price'],
                    'notes'       => $item['notes'],
                    'item_status' => 'pending',
                ]);

                // 3. Simpan varian opsi jika ada
                if (!empty($item['options'])) {
                    foreach ($item['options'] as $opt) {
                        OrderItemOption::create([
                            'order_item_id'       => $orderItem->id,
                            'menu_option_item_id' => $opt['id'],
                            'option_name'         => $opt['name'],
                            'additional_price'    => $opt['price'],
                        ]);
                    }
                }
            }

            DB::commit();

            // Kosongkan keranjang & Hapus Session
            $this->cart = [];
            session()->forget('customer_cart_' . $this->table->id);

            // Tampilkan Layar Sukses
            $this->showCartModal = false;
            $this->isOrderSubmitted = true;

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function orderMore()
    {
        $this->isOrderSubmitted = false;
    }

    public function render()
    {
        return view('components.customer.customer-menu')
            ->layout('components.layouts.customer');
    }
}
