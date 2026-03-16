<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;

// Rute untuk mendapatkan semua menu
Route::get('/menus', [MenuController::class, 'index']);

// Rute untuk mendapatkan detail 1 menu
Route::get('/menus/{id}', [MenuController::class, 'show']);

// Rute untuk membuat pesanan baru (Checkout)
Route::post('/orders', [OrderController::class, 'store']);

// Rute untuk Dapur mengupdate status masakan
Route::patch('/orders/{id}/kitchen-status', [OrderController::class, 'updateKitchenStatus']);

// Rute untuk Kasir memproses pembayaran
Route::post('/orders/{id}/pay', [OrderController::class, 'processPayment']);
