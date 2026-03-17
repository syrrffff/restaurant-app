<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Livewire\CustomerMenu;
use App\Livewire\KitchenDisplay;
use App\Livewire\CashierDashboard;
use App\Livewire\AdminMenu;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;
use App\Livewire\AdminDashboard;
use App\Livewire\AdminTable;
use App\Livewire\OrderHistory;
use App\Models\Order;
use App\Livewire\AdminUsers;


// --- RUTE PUBLIK (Bisa diakses tanpa login) ---
Route::get('/scan/{qr_token}', CustomerMenu::class);
Route::get('/login', Login::class)->name('login');

// --- RUTE TERLINDUNGI ---
Route::middleware('auth')->group(function () {

    // Hanya Admin yang bisa buka Master Data & QR
    Route::middleware('role:admin')->group(function() {
        Route::get('/admin/dashboard', AdminDashboard::class);
        Route::get('/admin/menus', AdminMenu::class);
        Route::get('/admin/tables/{id}/qr', [TableController::class, 'generateQr']);
        Route::get('/admin/tables', AdminTable::class);
        Route::get('/admin/users', AdminUsers::class)->name('admin.users');
    });

    // Dapur & Admin bisa buka layar dapur
    Route::middleware('role:kitchen')->group(function() {
        Route::get('/kitchen', KitchenDisplay::class);
    });

    // Kasir & Admin bisa buka layar kasir
    Route::middleware('role:cashier')->group(function() {
        Route::get('/cashier', CashierDashboard::class);
        Route::get('/order-history', OrderHistory::class)->name('order.history');
    });

    Route::get('/cashier/print/{order}', function (Order $order) {
        // Load relasi data yang dibutuhkan struk
        $order->load(['items.menu', 'cashier', 'table']);

        return view('cashier.print-receipt', compact('order'));
    })->name('cashier.print');

    Route::get('/logout', function () {
        Auth::logout();
        return redirect('/login');
    });

});
