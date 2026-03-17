<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Support\Facades\DB;

class OrderHistory extends Component
{
    use WithPagination;

    // Filter State
    public $search = "";
    public $startDate = "";
    public $endDate = "";
    public $statusFilter = ""; // 'paid', 'unpaid'
    public $typeFilter = ""; // 'dine_in', 'takeaway'

    // Detail State
    public $isDetailModalOpen = false;
    public $selectedOrder = null;
    public $orderLogs = [];

    protected $queryString = [
        "search",
        "startDate",
        "endDate",
        "statusFilter",
        "typeFilter",
    ];

    public function updating($field)
    {
        // Reset paginasi setiap kali filter diubah
        if (
            in_array($field, [
                "search",
                "startDate",
                "endDate",
                "statusFilter",
                "typeFilter",
            ])
        ) {
            $this->resetPage();
        }
    }

    public function openDetail($orderId)
    {
        $this->selectedOrder = Order::with(
            "table",
            "cashier",
            "items.menu",
            "items.selectedOptions",
        )->find($orderId);
        $this->orderLogs = OrderLog::with("user")
            ->where("order_id", $orderId)
            ->latest()
            ->get();
        $this->isDetailModalOpen = true;
    }

    public function closeDetail()
    {
        $this->isDetailModalOpen = false;
        $this->selectedOrder = null;
    }

    // Fungsi Log Aktivitas
    public static function logAction($orderId, $action, $description)
    {
        OrderLog::create([
            "order_id" => $orderId,
            "user_id" => auth()->id(),
            "action" => $action,
            "description" => $description,
        ]);
    }

    public function reprintOrder($orderId)
    {
        self::logAction($orderId, "reprinted", "Mencetak ulang struk tagihan.");
        $this->dispatch("print-receipt", order_id: $orderId);
    }

    public function deleteOrder($orderId)
    {
        try {
            DB::beginTransaction();
            $order = Order::find($orderId);

            if ($order) {
                // Bebaskan meja jika Dine In
                if ($order->table) {
                    $order->table->update(["status" => "available"]);
                }

                // Log sebelum dihapus (order_id diset null jika order dihapus, jadi simpan deskripsi lengkap)
                OrderLog::create([
                    "order_id" => null,
                    "user_id" => auth()->id(),
                    "action" => "deleted",
                    "description" =>
                        "Menghapus pesanan {$order->invoice_number} (Rp " .
                        number_format($order->total_amount, 0) .
                        ")",
                ]);

                $order->delete();
            }
            DB::commit();
            session()->flash(
                "success",
                "Riwayat pesanan berhasil dihapus secara permanen.",
            );
            $this->closeDetail();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash("error", "Gagal menghapus: " . $e->getMessage());
        }
    }

    private function buildQuery()
    {
        // Tambahkan 'items.menu' dan 'items.selectedOptions'
        $query = Order::with(
            "table",
            "cashier",
            "items.menu",
            "items.selectedOptions",
        );

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where(
                    "invoice_number",
                    "like",
                    "%" . $this->search . "%",
                )->orWhere("customer_name", "like", "%" . $this->search . "%");
            });
        }
        if (!empty($this->startDate)) {
            $query->whereDate("created_at", ">=", $this->startDate);
        }
        if (!empty($this->endDate)) {
            $query->whereDate("created_at", "<=", $this->endDate);
        }
        if (!empty($this->statusFilter)) {
            $query->where("payment_status", $this->statusFilter);
        }
        if (!empty($this->typeFilter)) {
            $query->where("order_type", $this->typeFilter);
        }

        return $query->latest();
    }

    public function exportExcel()
    {
        $orders = $this->buildQuery()->get();
        $fileName = "Laporan_Pesanan_" . date("d-m-Y") . ".csv";

        // Logika penentuan teks Rentang Waktu
        $startDateStr = $this->startDate
            ? \Carbon\Carbon::parse($this->startDate)->format("d M Y")
            : "Awal";
        $endDateStr = $this->endDate
            ? \Carbon\Carbon::parse($this->endDate)->format("d M Y")
            : "Sekarang";
        $rentangWaktu =
            $this->startDate || $this->endDate
                ? "$startDateStr s/d $endDateStr"
                : "Semua Waktu";

        // Hitung Total Penghasilan (Hanya yang Lunas)
        $totalPenghasilan = $orders
            ->where("payment_status", "paid")
            ->sum("total_amount");

        $callback = function () use (
            $orders,
            $rentangWaktu,
            $totalPenghasilan,
        ) {
            $file = fopen("php://output", "w");

            fputs($file, chr(0xef) . chr(0xbb) . chr(0xbf));

            // ==========================================
            // DESAIN "KOP LAPORAN" DI DALAM EXCEL
            // ==========================================
            fputcsv($file, ["LAPORAN RIWAYAT PESANAN RESTORAN"]);
            fputcsv($file, ["Rentang Waktu:", $rentangWaktu]);
            fputcsv($file, [
                "Tanggal Cetak:",
                \Carbon\Carbon::now()->format("d M Y, H:i"),
            ]);
            // TAMBAHKAN BARIS TOTAL PENGHASILAN DI SINI
            fputcsv($file, [
                "Total Pendapatan (Lunas):",
                "Rp " . number_format($totalPenghasilan, 0, ",", "."),
            ]);
            fputcsv($file, []); // Baris Kosong sebagai pembatas

            fputcsv($file, [
                "Tanggal",
                "Invoice",
                "Tipe",
                "Pelanggan/Meja",
                "Kasir",
                "Detail Pesanan & Varian",
                "Subtotal",
                "Pajak",
                "Total",
                "Status",
            ]);

            foreach ($orders as $order) {
                $customer =
                    $order->order_type == "takeaway"
                        ? $order->customer_name ?? "Takeaway"
                        : "Meja " . ($order->table->table_number ?? "-");

                $orderDetails = [];
                foreach ($order->items as $item) {
                    $menuName = $item->menu->name ?? "Menu Dihapus";
                    $options =
                        $item->selectedOptions->count() > 0
                            ? " (" .
                                implode(
                                    ", ",
                                    $item->selectedOptions
                                        ->pluck("option_name")
                                        ->toArray(),
                                ) .
                                ")"
                            : "";
                    $orderDetails[] =
                        $item->quantity . "x " . $menuName . $options;
                }
                $detailString = implode(" | ", $orderDetails);

                fputcsv($file, [
                    $order->created_at->format("d-M-Y H:i"),
                    $order->invoice_number,
                    $order->order_type == "takeaway" ? "Takeaway" : "Dine-In",
                    $customer,
                    $order->cashier->name ?? "Sistem",
                    $detailString,
                    $order->subtotal,
                    $order->tax_amount,
                    $order->total_amount,
                    $order->payment_status == "paid" ? "Lunas" : "Belum Lunas",
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, $fileName, [
            "Content-type" => "text/csv",
            "Cache-Control" => "no-cache, must-revalidate",
            "Pragma" => "no-cache",
        ]);
    }

    // 3. FUNGSI BARU: EKSPOR PDF SERVER-SIDE
    public function exportPDF()
    {
        $orders = $this->buildQuery()->get();

        $startDate = $this->startDate;
        $endDate = $this->endDate;

        // Hitung Total Penghasilan (Hanya yang Lunas)
        $totalPenghasilan = $orders
            ->where("payment_status", "paid")
            ->sum("total_amount");

        // Kirim variabel $totalPenghasilan ke View PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            "exports.orders-pdf",
            compact("orders", "startDate", "endDate", "totalPenghasilan"),
        )->setPaper("a4", "landscape");

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "Laporan_Pesanan_" . date("d-m-Y") . ".pdf");
    }

    public function render()
    {
        $orders = $this->buildQuery()->paginate(15);
        return view(
            "components.admin.order-history",
            compact("orders"),
        )->layout("components.layouts.app");
    }
}
