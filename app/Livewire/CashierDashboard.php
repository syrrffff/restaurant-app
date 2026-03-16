<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\Category;
use App\Models\MenuOptionItem;
use App\Models\OrderItemOption;
use App\Traits\WithConfirmation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CashierDashboard extends Component
{
    use WithConfirmation;

    public $categoriesList = [];

    // --- State Takeaway ---
    public $isTakeawayModalOpen = false;
    public $takeawayCart = [];
    public $customerName = '';

    // --- State Detail Order ---
    public $isDetailModalOpen = false;
    public $selectedOrderId = null;
    public $selectedOrder = null;

    // --- State Modal Opsi / Varian ---
    public $isOptionModalOpen = false;
    public $editMode = '';
    public $editingTargetId = null;
    public $selectedMenu = null;
    public $quantity = 1;
    public $selectedOptions = [];
    public $notes = '';

    public function mount()
    {
        $this->categoriesList = Category::where('is_active', true)
            ->with(['menus' => function($query) {
                $query->where('is_available', true)->with('options.items');
            }])->get();
    }

    // ==========================================
    // FUNGSI DETAIL ORDER (EXISTING)
    // ==========================================
    public function openDetailModal($orderId)
    {
        $this->selectedOrderId = $orderId;
        $this->loadSelectedOrder();
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal()
    {
        $this->isDetailModalOpen = false;
        $this->selectedOrderId = null;
        $this->selectedOrder = null;
    }

    public function loadSelectedOrder()
    {
        $this->selectedOrder = Order::with('table', 'items.menu.options.items', 'items.selectedOptions')->find($this->selectedOrderId);
    }

    public function removeOrderItem($itemId)
    {
        try {
            DB::beginTransaction();

            $orderItem = OrderItem::find($itemId);
            if ($orderItem) {
                $orderId = $orderItem->order_id;
                $order = Order::find($orderId);

                OrderItemOption::where('order_item_id', $orderItem->id)->delete();
                $orderItem->delete();

                $remainingItems = OrderItem::where('order_id', $order->id)->count();

                if ($remainingItems == 0) {
                    if ($order->table) {
                        $order->table->update(['status' => 'available', 'qr_token' => Str::random(10)]);
                    }
                    $order->delete();
                    $this->closeDetailModal();
                    session()->flash('success', 'Pesanan dibatalkan karena semua menu dihapus.');
                } else {
                    $newSubtotal = OrderItem::where('order_id', $order->id)->sum('total_price');
                    $newTax = $newSubtotal * 0.10;
                    $order->update([
                        'subtotal' => $newSubtotal,
                        'tax_amount' => $newTax,
                        'total_amount' => $newSubtotal + $newTax
                    ]);

                    $this->loadSelectedOrder();
                    session()->flash('success', 'Menu berhasil dihapus!');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    // ==========================================
    // FUNGSI TAKEAWAY
    // ==========================================
    public function openTakeawayModal()
    {
        $this->takeawayCart = [];
        $this->customerName = '';
        $this->isTakeawayModalOpen = true;
    }

    public function closeTakeawayModal()
    {
        $this->isTakeawayModalOpen = false;
    }

    public function removeTakeawayItem($index)
    {
        unset($this->takeawayCart[$index]);
        $this->takeawayCart = array_values($this->takeawayCart);
    }

    // ==========================================
    // FUNGSI MODAL OPSI / VARIAN (SHARED)
    // ==========================================
    public function openOptionModal($menuId, $mode, $targetId = null)
    {
        $this->selectedMenu = Menu::with('options.items')->find($menuId);
        $this->editMode = $mode;
        $this->editingTargetId = $targetId;
        $this->quantity = 1;
        $this->selectedOptions = [];
        $this->notes = '';

        if ($mode == 'edit_takeaway') {
            $cartItem = $this->takeawayCart[$targetId];
            $this->quantity = $cartItem['quantity'];
            $this->selectedOptions = $cartItem['raw_options'] ?? [];
            $this->notes = $cartItem['notes'] ?? '';

        } elseif ($mode == 'edit_existing') {
            $orderItem = OrderItem::with('selectedOptions')->find($targetId);
            $this->quantity = $orderItem->quantity;
            $this->notes = $orderItem->notes ?? '';

            $savedOptionItemIds = $orderItem->selectedOptions->pluck('menu_option_item_id')->toArray();

            foreach ($this->selectedMenu->options as $option) {
                $groupItemIds = $option->items->pluck('id')->toArray();
                $intersect = array_intersect($savedOptionItemIds, $groupItemIds);

                if (!empty($intersect)) {
                    if ($option->max_choices == 1) {
                        $this->selectedOptions[$option->id] = (string) reset($intersect);
                    } else {
                        foreach ($intersect as $val) {
                            $this->selectedOptions[$option->id][$val] = true;
                        }
                    }
                }
            }
        }

        $this->isOptionModalOpen = true;
    }

    public function closeOptionModal()
    {
        $this->isOptionModalOpen = false;
        $this->selectedMenu = null;
    }

    public function saveOptions()
    {
        // ==========================================
        // 1. VALIDASI OPSI WAJIB
        // ==========================================
        foreach ($this->selectedMenu->options as $option) {
            if ($option->is_required) {
                $hasSelection = false;

                if (isset($this->selectedOptions[$option->id])) {
                    $val = $this->selectedOptions[$option->id];

                    if (is_array($val)) {
                        foreach ($val as $isChecked) {
                            if ($isChecked) {
                                $hasSelection = true;
                                break;
                            }
                        }
                    } else {
                        if (!empty($val)) {
                            $hasSelection = true;
                        }
                    }
                }

                if (!$hasSelection) {
                    session()->flash('option_error', "Pilihan '{$option->name}' wajib diisi!");
                    return; // Hentikan proses jika ada yang kosong
                }
            }
        }

        // ==========================================
        // 2. JIKA LOLOS VALIDASI, LANJUTKAN SIMPAN
        // ==========================================
        $totalAdditionalPrice = 0;
        $selectedOptionsData = [];

        foreach ($this->selectedOptions as $optionId => $selectedValue) {
            if (is_array($selectedValue)) {
                foreach ($selectedValue as $itemId => $isChecked) {
                    if ($isChecked) {
                        $item = MenuOptionItem::find($itemId);
                        if ($item) {
                            $totalAdditionalPrice += $item->additional_price;
                            $selectedOptionsData[] = ['id' => $item->id, 'name' => $item->name, 'price' => $item->additional_price];
                        }
                    }
                }
            } else {
                $item = MenuOptionItem::find($selectedValue);
                if ($item) {
                    $totalAdditionalPrice += $item->additional_price;
                    $selectedOptionsData[] = ['id' => $item->id, 'name' => $item->name, 'price' => $item->additional_price];
                }
            }
        }

        $pricePerItem = $this->selectedMenu->base_price + $totalAdditionalPrice;
        $totalPrice = $pricePerItem * $this->quantity;

        if ($this->editMode == 'new_takeaway' || $this->editMode == 'edit_takeaway') {
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

            if ($this->editMode == 'edit_takeaway') {
                $this->takeawayCart[$this->editingTargetId] = $cartItem;
            } else {
                $this->takeawayCart[] = $cartItem;
            }
        }
        elseif ($this->editMode == 'edit_existing') {
            try {
                DB::beginTransaction();

                $orderItem = OrderItem::find($this->editingTargetId);
                $orderItem->update([
                    'quantity' => $this->quantity,
                    'total_price' => $totalPrice,
                    'notes' => $this->notes,
                ]);

                OrderItemOption::where('order_item_id', $orderItem->id)->delete();
                foreach ($selectedOptionsData as $opt) {
                    OrderItemOption::create([
                        'order_item_id' => $orderItem->id,
                        'menu_option_item_id' => $opt['id'],
                        'option_name' => $opt['name'],
                        'additional_price' => $opt['price'],
                    ]);
                }

                $order = Order::find($orderItem->order_id);
                $newSubtotal = OrderItem::where('order_id', $order->id)->sum('total_price');
                $newTax = $newSubtotal * 0.10;
                $order->update([
                    'subtotal' => $newSubtotal,
                    'tax_amount' => $newTax,
                    'total_amount' => $newSubtotal + $newTax
                ]);

                DB::commit();
                $this->loadSelectedOrder();
                session()->flash('success', 'Perubahan menu berhasil disimpan!');
            } catch (\Exception $e) {
                DB::rollback();
                session()->flash('error', 'Gagal update: ' . $e->getMessage());
            }
        }

        $this->closeOptionModal();
    }


    // ==========================================
    // FUNGSI PEMBAYARAN
    // ==========================================
    public function processTakeaway($paymentMethod)
    {
        $this->validate(['customerName' => 'required|string|max:255'], ['customerName.required' => 'Nama wajib diisi!']);
        if (empty($this->takeawayCart)) return;

        try {
            DB::beginTransaction();

            $subtotal = array_sum(array_column($this->takeawayCart, 'total_price'));
            $taxAmount = $subtotal * 0.10;
            $totalAmount = $subtotal + $taxAmount;
            $invoiceNumber = 'TA-' . strtoupper(Str::random(8));

            $order = Order::create([
                'table_id' => null, 'order_type' => 'takeaway', 'customer_name' => $this->customerName,
                'invoice_number' => $invoiceNumber, 'subtotal' => $subtotal, 'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount, 'kitchen_status' => 'pending', 'payment_status' => 'paid',
                'payment_method' => $paymentMethod, 'cashier_id' => auth()->id(),
            ]);

            foreach ($this->takeawayCart as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id, 'menu_id' => $item['menu_id'], 'quantity' => $item['quantity'],
                    'base_price' => $item['base_price'], 'total_price' => $item['total_price'],
                    'notes' => $item['notes'], 'item_status' => 'pending',
                ]);
                if (!empty($item['options'])) {
                    foreach ($item['options'] as $opt) {
                        OrderItemOption::create([
                            'order_item_id' => $orderItem->id, 'menu_option_item_id' => $opt['id'],
                            'option_name' => $opt['name'], 'additional_price' => $opt['price'],
                        ]);
                    }
                }
            }

            DB::commit();
            $this->closeTakeawayModal();
            $this->dispatch('print-receipt', order_id: $order->id);
            session()->flash('success', "Takeaway {$this->customerName} lunas!");
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    public function payCash($id) { $this->payOrder($id, 'Cash'); }
    public function payQRIS($id) { $this->payOrder($id, 'QRIS'); }
    public function payDebit($id) { $this->payOrder($id, 'Debit'); }

    public function payOrder($orderId, $paymentMethod)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update(['payment_status' => 'paid', 'payment_method' => $paymentMethod, 'cashier_id' => auth()->id()]);
            if ($order->table) {
                $order->table->update(['status' => 'available', 'qr_token' => Str::random(10)]);
            }
            $this->closeDetailModal();
            $this->dispatch('print-receipt', order_id: $order->id);
            session()->flash('success', "Pembayaran lunas!");
        }
    }

    public function render()
    {
        $orders = Order::with('table', 'items.selectedOptions')
                       ->where('payment_status', 'unpaid')
                       ->latest()
                       ->get();

        return view('cashier.cashier-dashboard', compact('orders'))
               ->layout('components.layouts.app');
    }
}
