<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\WithConfirmation;

class KitchenDisplay extends Component
{
    use WithConfirmation;

    public function startCooking($id)
    {
        $this->updateStatus($id, "cooking");
    }

    public function markReady($id)
    {
        $this->updateStatus($id, "ready"); // Status utama (Order) tetap 'ready'
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::find($orderId);

        if ($order) {
            // 1. Update status Tagihan Utama (tabel orders)
            $order->update(["kitchen_status" => $status]);

            // 2. Update status PER-ITEM (tabel order_items)
            if ($status == "cooking") {
                // Jika klik "Mulai Masak", ubah item 'pending' menjadi 'cooking'
                OrderItem::where("order_id", $order->id)
                    ->where("item_status", "pending")
                    ->update(["item_status" => "cooking"]);
            } elseif ($status == "ready") {
                // PERBAIKAN BUG: Gunakan 'done' untuk item_status sesuai skema database Anda
                OrderItem::where("order_id", $order->id)
                    ->where("item_status", "cooking")
                    ->update(["item_status" => "done"]);
            }

            $pesan = $status == "cooking" ? "mulai dimasak" : "siap disajikan";
            session()->flash(
                "success",
                "Pesanan Meja {$order->table->table_number} {$pesan}!",
            );
        }
    }

    public function render()
    {
        // Query tetap sama, karena yang 'done' akan otomatis dihilangkan dari layar dapur
        $orders = Order::with([
            "table",
            "items" => function ($q) {
                $q->whereIn("item_status", ["pending", "cooking"])->with(
                    "menu",
                    "selectedOptions",
                );
            },
        ])
            ->whereHas("items", function ($q) {
                $q->whereIn("item_status", ["pending", "cooking"]);
            })
            ->oldest("updated_at")
            ->get();

        return view(
            "components.kitchen.kitchen-display",
            compact("orders"),
        )->layout("components.layouts.app");
    }
}
