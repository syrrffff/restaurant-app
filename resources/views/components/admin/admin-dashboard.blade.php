<div wire:poll.15s class="p-6">

    <div class="mb-8 border-b border-gray-200 pb-4">
        <div class="flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Dashboard Utama</h2>
                <p class="text-sm text-gray-500 m-0">Ringkasan performa restoran Anda pada hari ini ({{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}).</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-green-600 bg-green-50 px-3 py-1.5 rounded-full border border-green-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Live Update
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl">
                💰
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</p>
                <h4 class="text-2xl font-bold text-gray-800">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-2xl">
                🛒
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Pesanan Hari Ini</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $todayOrdersCount }}</h4>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 text-2xl">
                🍲
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Porsi Terjual Hari Ini</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $todayItemsSold }} <span class="text-sm font-normal text-gray-500">Porsi</span></h4>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-2xl">
                📋
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Menu Aktif</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $activeMenusCount }} <span class="text-sm font-normal text-gray-500">Menu</span></h4>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="xl:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-gray-800">🔥 Top 5 Menu Terlaris</h3>
                <span class="text-xs text-gray-500">Sepanjang Waktu</span>
            </div>

            <div class="space-y-4">
                @forelse($topMenus as $index => $top)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition border border-transparent hover:border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600 text-sm">
                                #{{ $index + 1 }}
                            </div>

                            <div>
                                <h4 class="font-bold text-gray-800">{{ $top->menu->name ?? 'Menu Dihapus' }}</h4>
                                <p class="text-xs text-gray-500">{{ $top->menu->category->name ?? 'Tanpa Kategori' }}</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-lg font-bold text-blue-600">{{ $top->total_sold }}</span>
                            <span class="text-xs text-gray-500">Terjual</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <div class="text-4xl mb-2">🍽️</div>
                        <p class="text-sm">Belum ada data penjualan menu.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">🕒 Pesanan Terbaru</h3>

            <div class="space-y-4">
                @forelse($latestOrders as $order)
                    <div class="border-l-4 {{ $order->payment_status == 'paid' ? 'border-green-500' : 'border-yellow-500' }} pl-4 py-2">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold text-gray-800">Meja {{ $order->table->table_number ?? '?' }}</h4>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $order->payment_status == 'paid' ? 'Lunas' : 'Belum Bayar' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-end mt-2">
                            <div>
                                <p class="text-xs text-gray-500 font-mono">{{ $order->invoice_number }}</p>
                                <p class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <strong class="text-sm text-gray-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                        <div class="text-4xl mb-3">📝</div>
                        <p class="text-sm text-center">Belum ada pesanan masuk.</p>
                    </div>
                @endforelse
            </div>

            @if(count($latestOrders) > 0)
                <a href="/cashier" class="block text-center mt-5 text-sm font-bold text-blue-600 hover:text-blue-800 transition">
                    Lihat Semua Pesanan di Kasir ➔
                </a>
            @endif
        </div>

    </div>

</div>
