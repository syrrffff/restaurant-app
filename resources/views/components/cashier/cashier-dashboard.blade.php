<div wire:poll.10s class="p-6 relative"
     x-data="{ confirmOpen: @entangle('isConfirmOpen'), takeawayOpen: @entangle('isTakeawayModalOpen'), detailOpen: @entangle('isDetailModalOpen'), optionOpen: @entangle('isOptionModalOpen') }"
     @print-receipt.window="window.open('/cashier/print/' + $event.detail.order_id, '_blank')">

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms
             class="fixed top-5 right-5 z-50 bg-green-500 text-white px-6 py-3 rounded shadow-lg flex items-center gap-3">
            <span class="text-xl">✅</span><strong class="font-semibold">{{ session('success') }}</strong>
        </div>
    @endif
    @if (session()->has('warning'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.duration.500ms
             class="fixed top-5 right-5 z-[100] bg-yellow-500 text-white px-6 py-3 rounded shadow-lg flex items-center gap-3">
            <span class="text-xl">⚠️</span><strong class="font-semibold">{{ session('warning') }}</strong>
        </div>
    @endif

    <div class="flex justify-between items-center mb-8 border-b border-gray-200 pb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-1">Dashboard Kasir (Point of Sale)</h2>
            <p class="text-sm text-gray-500 m-0">Daftar Tagihan Belum Dibayar & Pembuatan Pesanan Takeaway</p>
        </div>
        <button wire:click="openTakeawayModal" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md flex items-center gap-2">
            🛍️ Buat Pesanan Takeaway
        </button>
    </div>

    <div class="flex flex-wrap gap-6 items-start">
        @forelse($orders as $order)
            <div class="bg-white w-full md:w-[320px] rounded-xl shadow-md border-t-4 {{ $order->order_type == 'takeaway' ? 'border-purple-500' : 'border-red-500' }} p-6 flex flex-col justify-between">

                <div>
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                        <h3 class="text-xl font-bold text-gray-800 m-0 truncate pr-2">
                            {{ $order->order_type == 'takeaway' ? '🥡 ' . ($order->customer_name ?? 'Takeaway') : ($order->table->table_number ?? '?') }}
                        </h3>
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-mono font-bold">{{ $order->invoice_number }}</span>
                    </div>

                    <div class="mb-5 text-center">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Total Tagihan</p>
                        <h2 class="text-3xl font-bold text-gray-800 m-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h2>
                        <p class="text-xs text-gray-400 mt-1">{{ count($order->items) }} Item</p>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg mb-6 max-h-32 overflow-y-auto">
                        <ul class="list-disc pl-4 m-0 space-y-2 text-xs text-gray-700">
                            @foreach($order->items as $item)
                                <li>
                                    <strong>{{ $item->quantity }}x</strong> {{ $item->menu->name }}
                                    @if($item->selectedOptions->count() > 0)
                                        <div class="text-gray-500 mt-0.5" style="font-size: 10px;">
                                            Varian: {{ implode(', ', $item->selectedOptions->pluck('option_name')->toArray()) }}
                                        </div>
                                    @endif
                                    @if($item->notes)
                                        <div class="text-orange-600 italic mt-0.5" style="font-size: 10px;">📝 {{ $item->notes }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <button wire:click="openDetailModal({{ $order->id }})" class="w-full py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-bold transition flex items-center justify-center gap-2">
                    Lihat Detail & Bayar ➔
                </button>
            </div>
        @empty
            <div class="w-full bg-white text-center p-12 rounded-xl border border-gray-200 shadow-sm text-gray-500">
                <div class="text-5xl mb-4">💰</div><h3 class="text-xl font-bold text-gray-800 mb-2">Semua Tagihan Lunas</h3>
            </div>
        @endforelse
    </div>

    @if($selectedOrder)
    <div x-show="detailOpen" style="display: none;" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4" @click.self="detailOpen = false; $wire.closeDetailModal()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">

            <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Detail Pesanan</h3>
                    <p class="text-sm text-gray-500 font-semibold">{{ $selectedOrder->order_type == 'takeaway' ? 'Takeaway - ' . $selectedOrder->customer_name : 'Dine-in - Meja ' . ($selectedOrder->table->table_number ?? '') }} ({{ $selectedOrder->invoice_number }})</p>
                </div>
                <button wire:click="closeDetailModal" class="text-gray-400 hover:text-red-500 font-bold text-2xl leading-none">&times;</button>
            </div>

            <div class="p-5 overflow-y-auto flex-1 bg-gray-50 space-y-3">
                @foreach($selectedOrder->items as $item)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800">{{ $item->quantity }}x {{ $item->menu->name }}</h4>
                            @if($item->selectedOptions->count() > 0)
                                <p class="text-xs text-gray-500 mt-1 font-medium">Varian: {{ implode(', ', $item->selectedOptions->pluck('option_name')->toArray()) }}</p>
                            @endif
                            @if($item->notes)
                                <p class="text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded mt-1 italic w-max">📝 {{ $item->notes }}</p>
                            @endif
                        </div>

                        <div class="text-right flex flex-col items-end justify-between h-full w-full sm:w-auto">
                            <strong class="text-gray-800 block mb-3 text-lg sm:text-base">Rp {{ number_format($item->total_price, 0, ',', '.') }}</strong>

                            <div class="flex gap-2 w-full sm:w-auto justify-end">
                                <button wire:click="openOptionModal({{ $item->menu_id }}, 'edit_existing', {{ $item->id }})" class="flex-1 sm:flex-none text-xs font-bold text-blue-600 bg-blue-50 px-4 py-1.5 rounded border border-blue-100 hover:bg-blue-100 shadow-sm">
                                    ✎ Edit
                                </button>
                                <button wire:click="showConfirm('removeOrderItem', {{ $item->id }}, 'Yakin ingin menghapus {{ $item->menu->name }} dari pesanan?', 'danger')" class="flex-1 sm:flex-none text-xs font-bold text-red-600 bg-red-50 px-4 py-1.5 rounded border border-red-100 hover:bg-red-100 shadow-sm">
                                    🗑️ Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-5 bg-white border-t border-gray-200 shrink-0">
                <div class="flex justify-between items-center mb-1 text-sm text-gray-500">
                    <span>Subtotal</span><span>Rp {{ number_format($selectedOrder->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-4 text-sm text-gray-500 pb-3 border-b border-gray-100">
                    <span>Pajak (10%)</span><span>Rp {{ number_format($selectedOrder->tax_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-5">
                    <span class="text-gray-800 font-bold">TOTAL TAGIHAN</span>
                    <strong class="text-2xl text-red-600">Rp {{ number_format($selectedOrder->total_amount, 0, ',', '.') }}</strong>
                </div>

                <p class="text-xs font-bold text-gray-500 mb-2 uppercase">Pilih Metode Pembayaran</p>
                <div class="flex gap-3">
                    <button wire:click="showConfirm('payCash', {{ $selectedOrder->id }}, 'Proses pelunasan dengan Cash?', 'warning')" class="flex-1 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold shadow-sm">💵 Cash</button>
                    <button wire:click="showConfirm('payQRIS', {{ $selectedOrder->id }}, 'Proses pelunasan dengan QRIS?', 'warning')" class="flex-1 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-bold shadow-sm">📱 QRIS</button>
                    <button wire:click="showConfirm('payDebit', {{ $selectedOrder->id }}, 'Proses pelunasan dengan Debit?', 'warning')" class="flex-1 py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-lg font-bold shadow-sm">💳 Debit</button>
                </div>
            </div>

        </div>
    </div>
    @endif

    <div x-show="takeawayOpen" style="display: none;" x-transition.opacity class="fixed inset-0 z-[50] flex items-center justify-center bg-black/60 p-4" @click.self="takeawayOpen = false; $wire.closeTakeawayModal()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl h-[90vh] flex overflow-hidden">

            <div class="w-2/3 bg-gray-100 overflow-y-auto flex flex-col">
                <div class="p-5 bg-white border-b border-gray-200 sticky top-0 z-10">
                    <h3 class="text-xl font-bold text-gray-800">Pilih Menu Takeaway</h3>
                </div>
                <div class="p-5 space-y-8">
                    @foreach($categoriesList as $category)
                        @if($category->menus->count() > 0)
                            <div>
                                <h4 class="font-bold text-gray-800 mb-3 border-b border-gray-200 pb-2 uppercase tracking-wider text-sm">{{ $category->name }}</h4>
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($category->menus as $menu)
                                        <button wire:click="openOptionModal({{ $menu->id }}, 'new_takeaway')" class="bg-white p-4 rounded-xl shadow-sm border border-transparent hover:border-blue-500 hover:shadow-md transition text-left focus:outline-none flex flex-col justify-between h-full">
                                            <div>
                                                <h5 class="font-bold text-gray-800 leading-tight text-sm">{{ $menu->name }}</h5>
                                            </div>
                                            <p class="text-blue-600 font-bold mt-3 text-sm border-t border-gray-50 pt-2 w-full">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</p>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="w-1/3 bg-white flex flex-col relative border-l border-gray-200 shadow-xl z-20">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                    <h3 class="font-bold text-lg text-gray-800">Keranjang</h3>
                    <button wire:click="closeTakeawayModal" class="text-gray-400 hover:text-red-500 font-bold text-xl">&times;</button>
                </div>

                <div class="p-4 bg-blue-50 border-b border-blue-100 shrink-0">
                    <label class="block text-xs font-bold text-blue-800 mb-1">Nama Pelanggan *</label>
                    <input type="text" wire:model="customerName" placeholder="Contoh: Mas Budi" class="w-full px-3 py-2 border border-blue-200 rounded outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm">
                    @error('customerName') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="p-4 flex-1 overflow-y-auto space-y-3 bg-gray-50">
                    @forelse($takeawayCart as $index => $item)
                        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm relative">
                            <button wire:click="removeTakeawayItem({{ $index }})" class="absolute top-2 right-2 text-red-400 hover:text-red-600 font-bold">&times;</button>
                            <h5 class="text-sm font-bold text-gray-800 pr-5">{{ $item['quantity'] }}x {{ $item['name'] }}</h5>

                            @if(isset($item['options']) && count($item['options']) > 0)
                                <p class="text-[10px] text-gray-500 mt-1">{{ implode(', ', array_column($item['options'], 'name')) }}</p>
                            @endif
                            @if(!empty($item['notes']))
                                <p class="text-[10px] text-orange-600 mt-1 italic w-max">📝 {{ $item['notes'] }}</p>
                            @endif

                            <div class="flex justify-between items-end mt-2">
                                <span class="text-xs font-bold text-gray-800">Rp {{ number_format($item['total_price'], 0, ',', '.') }}</span>
                                <button wire:click="openOptionModal({{ $item['menu_id'] }}, 'edit_takeaway', {{ $index }})" class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-100 hover:bg-blue-100">✎ Edit</button>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-400">
                            <div class="text-4xl mb-2">🛒</div><p class="text-center text-sm">Keranjang kosong.</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-4 bg-white border-t border-gray-200 shrink-0 shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
                    @php
                        $taSubtotal = array_sum(array_column($takeawayCart, 'total_price'));
                        $taTax = $taSubtotal * 0.10;
                    @endphp
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-800 font-bold">Total Tagihan</span>
                        <strong class="text-2xl text-blue-600">Rp {{ number_format($taSubtotal + $taTax, 0, ',', '.') }}</strong>
                    </div>
                    @if(count($takeawayCart) > 0)
                        <div class="grid grid-cols-3 gap-2 mt-2">
                            <button wire:click="processTakeaway('Cash')" class="py-2.5 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-bold shadow-sm">Cash</button>
                            <button wire:click="processTakeaway('QRIS')" class="py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-bold shadow-sm">QRIS</button>
                            <button wire:click="processTakeaway('Debit')" class="py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded text-sm font-bold shadow-sm">Debit</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($isOptionModalOpen && $selectedMenu)
    <div x-show="optionOpen" style="display: none;" x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 p-4" @click.self="optionOpen = false; $wire.closeOptionModal()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Pilih Varian: {{ $selectedMenu->name }}</h3>
                <button wire:click="closeOptionModal" class="text-gray-400 hover:text-red-500 font-bold text-2xl leading-none">&times;</button>
            </div>

            @if (session()->has('option_error'))
                <div class="px-5 pt-4 shrink-0">
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-sm animate-pulse">
                        <span class="text-lg">⚠️</span> {{ session('option_error') }}
                    </div>
                </div>
            @endif

            <div class="p-5 overflow-y-auto max-h-[60vh] space-y-4">
                @foreach($selectedMenu->options as $option)
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-2">{{ $option->name }} @if($option->is_required) <span class="text-red-500 text-xs">*Wajib</span> @endif</h4>
                        <div class="space-y-2">
                            @foreach($option->items as $item)
                                <label class="flex justify-between items-center w-full cursor-pointer bg-white px-3 py-2 rounded border border-gray-200 hover:border-blue-400">
                                    <div class="flex items-center gap-2">
                                        @if($option->max_choices == 1)
                                            <input type="radio" wire:model="selectedOptions.{{ $option->id }}" value="{{ $item->id }}" class="w-4 h-4 text-blue-600">
                                        @else
                                            <input type="checkbox" wire:model="selectedOptions.{{ $option->id }}.{{ $item->id }}" class="w-4 h-4 text-blue-600 rounded">
                                        @endif
                                        <span class="text-gray-700 text-sm font-medium">{{ $item->name }}</span>
                                    </div>
                                    @if($item->additional_price > 0)
                                        <span class="text-gray-500 text-xs">+Rp{{ number_format($item->additional_price, 0, ',', '.') }}</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex gap-4">
                    <div class="w-1/3">
                        <h4 class="font-bold text-gray-800 mb-2 text-sm">Qty</h4>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="$set('quantity', {{ $quantity > 1 ? $quantity - 1 : 1 }})" class="w-8 h-8 rounded bg-gray-200 font-bold">-</button>
                            <span class="text-lg font-bold w-6 text-center">{{ $quantity }}</span>
                            <button type="button" wire:click="$set('quantity', {{ $quantity + 1 }})" class="w-8 h-8 rounded bg-blue-100 text-blue-600 font-bold">+</button>
                        </div>
                    </div>
                    <div class="w-2/3">
                        <h4 class="font-bold text-gray-800 mb-2 text-sm">Catatan</h4>
                        <textarea wire:model="notes" rows="1" class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 outline-none text-sm" placeholder="Contoh: Pedas..."></textarea>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-white">
                <button wire:click="saveOptions" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition">
                    {{ $editMode == 'new_takeaway' ? 'Tambahkan ke Keranjang' : 'Simpan Perubahan' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    @include('components.confirm-modal')
</div>
