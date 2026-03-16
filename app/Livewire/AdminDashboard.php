<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public function render()
    {
        $today = Carbon::today();

        // 1. Total Pendapatan Hari Ini (Hanya dari pesanan yang sudah 'paid')
        $todayRevenue = Order::whereDate('created_at', $today)
                             ->where('payment_status', 'paid')
                             ->sum('total_amount');

        // 2. Total Pesanan Hari Ini (Semua status pesanan)
        $todayOrdersCount = Order::whereDate('created_at', $today)->count();

        // 3. Menu Terjual Hari Ini (Jumlah porsi dari pesanan yang sudah dibayar)
        $todayItemsSold = OrderItem::whereHas('order', function ($query) use ($today) {
            $query->whereDate('created_at', $today)
                  ->where('payment_status', 'paid');
        })->sum('quantity');

        // 4. Jumlah Menu Aktif saat ini
        $activeMenusCount = Menu::where('is_available', true)->count();

        // 5. Pesanan Terbaru (5 Pesanan terakhir masuk)
        $latestOrders = Order::with('table')
                             ->latest()
                             ->take(5)
                             ->get();

        // 6. Menu Terlaris (Top 5 Menu paling banyak dibeli sepanjang waktu)
        $topMenus = OrderItem::select('menu_id', DB::raw('SUM(quantity) as total_sold'))
                             ->with('menu')
                             ->groupBy('menu_id')
                             ->orderByDesc('total_sold')
                             ->take(5)
                             ->get();

        return view('admin.admin-dashboard', compact(
            'todayRevenue',
            'todayOrdersCount',
            'todayItemsSold',
            'activeMenusCount',
            'latestOrders',
            'topMenus'
        ))->layout('components.layouts.app');
    }
}
