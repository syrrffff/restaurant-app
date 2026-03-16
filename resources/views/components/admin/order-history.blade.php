<div class="p-6 relative"
     x-data="{ detailOpen: @entangle('isDetailModalOpen') }"
     @print-receipt.window="window.open('/cashier/print/' + $event.detail.order_id, '_blank')">

    <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-1">Riwayat Transaksi (Order History)</h2>
            <p class="text-sm text-gray-500 m-0">Pantau, filter, dan kelola seluruh riwayat pesanan serta log aktivitas.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportPDF" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg flex items-center gap-2 shadow-sm">
                <span wire:loading.remove wire:target="exportPDF">📄 Export PDF</span>
                <span wire:loading wire:target="exportPDF">⏳ Memproses PDF...</span>
            </button>

            <button wire:click="exportExcel" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg flex items-center gap-2 shadow-sm">
                <span wire:loading.remove wire:target="exportExcel">📊 Export Excel</span>
                <span wire:loading wire:target="exportExcel">⏳ Mengunduh...</span>
            </button>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-600 mb-1">Pencarian</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. Invoice atau Nama Pelanggan..." class="w-full px-3 py-2 border border-gray-300 rounded outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Dari Tanggal</label>
            <input type="date" wire:model.live="startDate" class="w-full px-3 py-2 border border-gray-300 rounded outline-none text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Sampai Tanggal</label>
            <input type="date" wire:model.live="endDate" class="w-full px-3 py-2 border border-gray-300 rounded outline-none text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
            <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded outline-none text-sm bg-white">
                <option value="">Semua Status</option>
                <option value="paid">Lunas</option>
                <option value="unpaid">Belum Lunas</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="px-5 py-3 font-semibold">Tanggal</th>
                        <th class="px-5 py-3 font-semibold">Invoice</th>
                        <th class="px-5 py-3 font-semibold">Tipe & Pelanggan</th>
                        <th class="px-5 py-3 font-semibold">Kasir</th>
                        <th class="px-5 py-3 font-semibold">Total Nominal</th>
                        <th class="px-5 py-3 font-semibold text-center">Status</th>
                        <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($orders as $order)
                        <tr class="border-b border-gray-100 hover:bg-blue-50 transition">
                            <td class="px-5 py-3 text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3 font-mono font-bold text-blue-600">{{ $order->invoice_number }}</td>
                            <td class="px-5 py-3">
                                <span class="font-bold text-gray-800">{{ $order->order_type == 'takeaway' ? '🥡 Takeaway' : '🍽️ Dine-In' }}</span><br>
                                <span class="text-xs text-gray-500">{{ $order->order_type == 'takeaway' ? 'A/n: ' . ($order->customer_name ?? '-') : 'Meja ' . ($order->table->table_number ?? '-') }}</span>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $order->cashier->name ?? 'Sistem' }}</td>
                            <td class="px-5 py-3 font-bold text-gray-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-1 rounded text-xs font-bold {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $order->payment_status == 'paid' ? 'LUNAS' : 'UNPAID' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <button wire:click="openDetail({{ $order->id }})" class="px-3 py-1.5 bg-gray-800 text-white text-xs font-bold rounded hover:bg-gray-900 shadow-sm">Lihat Detail</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">Tidak ada data pesanan yang sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $orders->links() }}</div>
    </div>

    @if($selectedOrder)
    <div x-show="detailOpen" style="display: none;" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4" @click.self="detailOpen = false; $wire.closeDetail()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden">

            <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Detail & Log Pesanan</h3>
                    <p class="text-sm text-gray-500 font-mono">{{ $selectedOrder->invoice_number }}</p>
                </div>
                <div class="flex gap-2">
                    <button wire:click="reprintOrder({{ $selectedOrder->id }})" class="px-3 py-1.5 bg-blue-100 text-blue-700 font-bold rounded text-sm hover:bg-blue-200">🖨️ Re-Print Struk</button>
                    <a href="/cashier" class="px-3 py-1.5 bg-yellow-100 text-yellow-700 font-bold rounded text-sm hover:bg-yellow-200">✏️ Buka di POS</a>
                    <button onclick="confirm('Hapus PERMANEN pesanan ini?') || event.stopImmediatePropagation()" wire:click="deleteOrder({{ $selectedOrder->id }})" class="px-3 py-1.5 bg-red-100 text-red-700 font-bold rounded text-sm hover:bg-red-200">🗑️ Hapus</button>
                    <button wire:click="closeDetail" class="ml-4 text-gray-400 hover:text-red-500 font-bold text-2xl leading-none">&times;</button>
                </div>
            </div>

            <div class="flex flex-1 overflow-hidden">
                <div class="w-2/3 p-5 overflow-y-auto border-r border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">Rincian Menu</h4>
                    <table class="w-full text-sm text-left">
                        <thead class="text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="pb-2">Item</th>
                                <th class="pb-2 text-center">Harga</th>
                                <th class="pb-2 text-center">Qty</th>
                                <th class="pb-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedOrder->items as $item)
                                <tr class="border-b border-gray-50">
                                    <td class="py-3">
                                        <strong class="text-gray-800">{{ $item->menu->name ?? 'Menu Dihapus' }}</strong>
                                        @if($item->selectedOptions->count() > 0)
                                            <p class="text-[10px] text-gray-500 mt-1">Varian: {{ implode(', ', $item->selectedOptions->pluck('option_name')->toArray()) }}</p>
                                        @endif
                                        @if($item->notes)
                                            <p class="text-[10px] text-orange-600 mt-1 italic">Ket: {{ $item->notes }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">Rp {{ number_format($item->total_price / $item->quantity, 0, ',', '.') }}</td>
                                    <td class="py-3 text-center font-bold">{{ $item->quantity }}</td>
                                    <td class="py-3 text-right font-bold text-gray-800">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4 flex flex-col items-end text-sm space-y-1">
                        <p class="text-gray-600">Subtotal: Rp {{ number_format($selectedOrder->subtotal, 0, ',', '.') }}</p>
                        <p class="text-gray-600">Pajak (10%): Rp {{ number_format($selectedOrder->tax_amount, 0, ',', '.') }}</p>
                        <h4 class="text-lg font-bold text-gray-800 mt-2 pt-2 border-t border-gray-200">Total: Rp {{ number_format($selectedOrder->total_amount, 0, ',', '.') }}</h4>
                    </div>
                </div>

                <div class="w-1/3 bg-gray-50 p-5 overflow-y-auto">
                    <h4 class="font-bold text-gray-800 mb-3 border-b border-gray-200 pb-2">Audit Log (Aktivitas)</h4>
                    <div class="space-y-4 relative before:absolute before:inset-0 before:ml-2 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-300 before:to-transparent">

                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <div class="flex items-center justify-center w-5 h-5 rounded-full border border-white bg-green-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2"></div>
                            <div class="w-[calc(100%-2rem)] md:w-[calc(50%-1.5rem)] bg-white p-3 rounded shadow-sm border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">{{ $selectedOrder->created_at->format('d/m H:i') }}</p>
                                <p class="text-xs font-bold text-gray-800">Pesanan Dibuat</p>
                                <p class="text-[10px] text-gray-600 mt-1">Oleh: {{ $selectedOrder->cashier->name ?? 'Customer (QR)' }}</p>
                            </div>
                        </div>

                        @foreach($orderLogs as $log)
                            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                <div class="flex items-center justify-center w-5 h-5 rounded-full border border-white bg-blue-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2"></div>
                                <div class="w-[calc(100%-2rem)] md:w-[calc(50%-1.5rem)] bg-white p-3 rounded shadow-sm border border-gray-100">
                                    <p class="text-xs text-gray-500 mb-1">{{ $log->created_at->format('d/m H:i') }}</p>
                                    <p class="text-xs font-bold text-gray-800 capitalize">{{ $log->action }}</p>
                                    <p class="text-[10px] text-gray-600 mt-1">{{ $log->description }}</p>
                                    <p class="text-[10px] font-bold text-blue-600 mt-1">Oleh: {{ $log->user->name ?? 'Sistem' }}</p>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        /* Sembunyikan elemen lain saat mencetak PDF menggunakan browser */
        @media print {
            body * { visibility: hidden; }
            .bg-white.rounded-xl.shadow-sm.border.border-gray-200.overflow-hidden,
            .bg-white.rounded-xl.shadow-sm.border.border-gray-200.overflow-hidden * {
                visibility: visible;
            }
            .bg-white.rounded-xl.shadow-sm.border.border-gray-200.overflow-hidden {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none;
                box-shadow: none;
            }
        }
    </style>
</div>
