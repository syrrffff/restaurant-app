<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\MenuOptionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi input dari frontend
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items'    => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.options'  => 'nullable|array', // Varian opsional
            'items.*.options.*.menu_option_item_id' => 'required|exists:menu_option_items,id',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $invoiceNumber = 'INV-' . strtoupper(Str::random(8)); // Contoh: INV-A1B2C3D4

            // 2. Buat Draft Order (Total harga diisi 0 dulu, akan diupdate nanti)
            $order = Order::create([
                'table_id'       => $request->table_id,
                'invoice_number' => $invoiceNumber,
                'subtotal'       => 0,
                'tax_amount'     => 0,
                'total_amount'   => 0,
                'kitchen_status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            // 3. Looping setiap menu yang dipesan
            foreach ($request->items as $item) {
                $menu = Menu::find($item['menu_id']);
                $itemTotalPrice = $menu->base_price;

                // Hitung tambahan harga dari varian jika ada
                $selectedOptionsData = [];
                if (isset($item['options'])) {
                    foreach ($item['options'] as $opt) {
                        $optionItem = MenuOptionItem::find($opt['menu_option_item_id']);
                        $itemTotalPrice += $optionItem->additional_price;

                        // Simpan data varian sementara
                        $selectedOptionsData[] = [
                            'menu_option_item_id' => $optionItem->id,
                            'option_name'         => $optionItem->name,
                            'additional_price'    => $optionItem->additional_price,
                        ];
                    }
                }

                // Kalkulasi total per item dikali kuantitas
                $finalItemPrice = $itemTotalPrice * $item['quantity'];
                $subtotal += $finalItemPrice;

                // Simpan ke tabel order_items
                $orderItem = OrderItem::create([
                    'order_id'    => $order->id,
                    'menu_id'     => $menu->id,
                    'quantity'    => $item['quantity'],
                    'base_price'  => $menu->base_price,
                    'total_price' => $finalItemPrice,
                    'notes'       => $item['notes'] ?? null,
                    'item_status' => 'pending',
                ]);

                // Simpan ke tabel order_item_options
                foreach ($selectedOptionsData as $optionData) {
                    $orderItem->selectedOptions()->create($optionData);
                }
            }

            // 4. Update Total Harga di Tabel Order
            $taxAmount = $subtotal * 0.10; // Contoh pajak 10%
            $totalAmount = $subtotal + $taxAmount;

            $order->update([
                'subtotal'     => $subtotal,
                'tax_amount'   => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data'    => $order->load('items.selectedOptions')
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Update status pesanan oleh Dapur
     */
    public function updateKitchenStatus(Request $request, $id)
    {
        $request->validate([
            'kitchen_status' => 'required|in:pending,cooking,ready,served'
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        $order->update([
            'kitchen_status' => $request->kitchen_status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status dapur berhasil diperbarui menjadi ' . $request->kitchen_status,
            'data'    => $order
        ], 200);
    }

    /**
     * Proses pembayaran oleh Kasir
     */
    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string', // Contoh: Cash, QRIS, Debit
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Pesanan ini sudah dibayar'], 400);
        }

        // Update status order
        $order->update([
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method
        ]);

        // Bebaskan kembali meja agar bisa dipakai pelanggan lain
        $table = \App\Models\Table::find($order->table_id);
        if ($table) {
            $table->update(['status' => 'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diproses, meja sekarang tersedia kembali',
            'data'    => $order
        ], 200);
    }
}
