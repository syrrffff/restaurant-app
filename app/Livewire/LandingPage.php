<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Facades\Mail; // Tambahkan ini

class LandingPage extends Component
{
    // Variabel Form
    public $name = '';
    public $email = '';
    public $subject = '';
    public $message = '';

    public function sendMessage()
    {
        // 1. Validasi Input
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            // 2. Format Isi Email (HTML)
            $htmlMessage = "
                <h3>Pesan Baru dari Website Restoran</h3>
                <p><strong>Nama:</strong> {$this->name}</p>
                <p><strong>Email:</strong> {$this->email}</p>
                <p><strong>Subject:</strong> {$this->subject}</p>
                <p><strong>Pesan:</strong><br/>" . nl2br(e($this->message)) . "</p>
            ";

            // 3. Kirim Email
            Mail::html($htmlMessage, function ($mail) {
                // Ganti email di bawah dengan Email Anda (Tujuan)
                $mail->to('sijunior084@gmail.com')
                     ->subject('Kontak Web: ' . $this->subject)
                     // Fitur agar Anda bisa langsung membalas pesan ini ke email customer
                     ->replyTo($this->email, $this->name);
            });

            // 4. Jika sukses, kosongkan form dan tampilkan notifikasi
            session()->flash('success_message', 'Pesan berhasil dikirim! Kami akan segera menghubungi Anda.');
            $this->reset(['name', 'email', 'subject', 'message']);

        } catch (\Exception $e) {
            // Jika gagal (biasanya karena SMTP belum disetting)
            session()->flash('error_message', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categories = \App\Models\Category::where('is_active', true)
            ->with(['menus' => function($q) {
                $q->where('is_available', true);
            }])
            ->get();

        // LOGIKA BARU: Cari 6 menu paling banyak dipesan dari riwayat OrderItem
        $topMenuIds = \App\Models\OrderItem::select('menu_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('menu_id')
            ->orderByDesc('total_sold')
            ->take(6)
            ->pluck('menu_id');

        // Tarik detail menu berdasarkan ID terlaris tersebut
        $topMenus = \App\Models\Menu::whereIn('id', $topMenuIds)->where('is_available', true)->get();

        // Fallback (Cadangan): Jika aplikasi masih baru dan belum ada transaksi pesanan sama sekali
        if ($topMenus->isEmpty()) {
            $topMenus = \App\Models\Menu::where('is_available', true)->inRandomOrder()->take(6)->get();
        }

        // Kirim variabel $topMenus ke tampilan
        return view('landing-page', compact('categories', 'topMenus'))
               ->layout('components.layouts.guest'); 
    }
}
