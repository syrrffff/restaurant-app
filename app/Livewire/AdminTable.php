<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Table;
use Illuminate\Support\Str;
use App\Traits\WithConfirmation;

class AdminTable extends Component
{
    use WithPagination, WithConfirmation;

    public $search = "";

    // Kontrol Modal Form Meja
    public $isModalOpen = false;
    public $table_id, $table_number;
    public $status = "available";

    // Kontrol Modal Konfirmasi Kustom
    public $isConfirmOpen = false;
    public $confirmType = ""; // 'delete' atau 'regenerate'
    public $confirmId = null;
    public $confirmMessage = "";

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- FUNGSI MODAL FORM MEJA ---
    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(["table_id", "table_number"]);
        $this->status = "available";
    }

    // --- FUNGSI MODAL KONFIRMASI KUSTOM ---
    public function confirmAction($action, $id)
    {
        $this->confirmType = $action;
        $this->confirmId = $id;

        if ($action === "delete") {
            $this->confirmMessage =
                "Yakin ingin menghapus meja ini? Pastikan tidak ada pesanan aktif di meja ini.";
        } elseif ($action === "regenerate") {
            $this->confirmMessage =
                "Yakin ingin mengganti QR Token? QR Code yang lama otomatis tidak akan bisa digunakan lagi oleh pelanggan.";
        }

        $this->isConfirmOpen = true; // Buka pop-up konfirmasi
    }

    public function closeConfirm()
    {
        $this->isConfirmOpen = false;
        $this->confirmType = "";
        $this->confirmId = null;
    }

    // Eksekusi aksi jika tombol "Ya, Lanjutkan" diklik
    public function executeAction()
    {
        if ($this->confirmType === "delete") {
            $this->deleteTable($this->confirmId);
        } elseif ($this->confirmType === "regenerate") {
            $this->regenerateToken($this->confirmId);
        }

        $this->closeConfirm();
    }

    // --- FUNGSI CRUD MEJA ---
    public function saveTable()
    {
        $this->validate([
            "table_number" =>
                "required|string|max:50|unique:tables,table_number," .
                $this->table_id,
            "status" => "required|in:available,occupied",
        ]);

        try {
            if ($this->table_id) {
                $table = Table::find($this->table_id);
                $table->update([
                    "table_number" => $this->table_number,
                    "status" => $this->status,
                ]);
                $pesan = "Data meja berhasil diperbarui!";
            } else {
                Table::create([
                    "table_number" => $this->table_number,
                    "status" => $this->status,
                    "qr_token" => Str::random(10),
                ]);
                $pesan = "Meja baru berhasil ditambahkan!";
            }

            session()->flash("success", $pesan);
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash("error", "Terjadi kesalahan: " . $e->getMessage());
        }
    }

    public function editTable($id)
    {
        $table = Table::find($id);
        if ($table) {
            $this->table_id = $table->id;
            $this->table_number = $table->table_number;
            $this->status = $table->status;

            $this->isModalOpen = true;
        }
    }

    public function deleteTable($id)
    {
        $table = Table::find($id);
        if ($table) {
            $table->delete();
            session()->flash("success", "Meja berhasil dihapus!");
        }
    }

    public function regenerateToken($id)
    {
        $table = Table::find($id);
        if ($table) {
            $table->update(["qr_token" => Str::random(10)]);
            session()->flash("success", "QR Token berhasil diperbarui!");
        }
    }

    public function render()
    {
        $tables = Table::where(
            "table_number",
            "like",
            "%" . $this->search . "%",
        )
            ->latest()
            ->paginate(10);

        return view("components.admin.admin-table", compact("tables"))->layout(
            "components.layouts.app",
        );
    }
}
