<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password;

    public function authenticate()
{
    $this->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
        session()->regenerate();

        // Ambil data user yang baru saja login
        $user = Auth::user();

        // Logika Pengalihan (Redirect) berdasarkan Role
        if ($user->role === 'admin') {
            return redirect()->to('/admin/menus');
        } elseif ($user->role === 'kitchen') {
            return redirect()->to('/kitchen');
        } elseif ($user->role === 'cashier') {
            return redirect()->to('/cashier');
        }

        // Default jika role tidak dikenal
        return redirect()->to('/');
    }

    session()->flash('error', 'Email atau Password salah!');
}

    public function render()
    {
        return view('auth.auth-login')
        ->layout('components.layouts.guest');
    }
}
