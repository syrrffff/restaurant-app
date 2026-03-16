<?php

namespace App\Traits;

trait WithConfirmation
{
    public $isConfirmOpen = false;
    public $confirmMethod = ''; // Nama fungsi yang akan dieksekusi (misal: 'deleteTable')
    public $confirmId = null;   // ID data yang akan diproses
    public $confirmMessage = '';
    public $confirmTheme = 'warning'; // 'warning' (Kuning/Biru) atau 'danger' (Merah)

    // Fungsi untuk memanggil Pop-up
    public function showConfirm($method, $id, $message, $theme = 'warning')
    {
        $this->confirmMethod = $method;
        $this->confirmId = $id;
        $this->confirmMessage = $message;
        $this->confirmTheme = $theme;
        
        $this->isConfirmOpen = true;
    }

    // Fungsi untuk menutup Pop-up
    public function closeConfirm()
    {
        $this->isConfirmOpen = false;
        $this->confirmMethod = '';
        $this->confirmId = null;
    }

    // Fungsi untuk mengeksekusi aksi jika tombol "Ya" diklik
    public function executeConfirm()
    {
        $method = $this->confirmMethod;
        
        // Cek apakah fungsi yang dipanggil ada di komponen Livewire
        if (method_exists($this, $method)) {
            $this->$method($this->confirmId); // Jalankan fungsi tersebut
        }
        
        $this->closeConfirm();
    }
}
