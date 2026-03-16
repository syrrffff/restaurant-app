<div class="relative pb-24">

    @if($isOrderSubmitted)
        <div class="fixed inset-0 z-[200] flex flex-col items-center justify-center bg-white p-6 text-center max-w-md mx-auto">
            <div class="text-7xl mb-6">🎉</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Pesanan Berhasil!</h2>
            <p class="text-gray-500 mb-8">Pesanan Anda telah dikirim ke dapur dan sedang disiapkan. Mohon tunggu sebentar di <strong>Meja {{ $table->table_number }}</strong>.</p>

            <button wire:click="orderMore" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg transition">
                + Tambah Pesanan Lain
            </button>
        </div>
    @endif

    <div class="sticky top-0 z-30 bg-white shadow-sm px-5 py-4 flex justify-between items-center border-b border-gray-100">
        <div>
            <h2 class="font-bold text-xl text-gray-800">Menu Restoran</h2>
            <p class="text-xs text-green-600 font-semibold mt-0.5">Meja Anda: {{ $table->table_number }}</p>
        </div>
        <div class="text-3xl">🍽️</div>
    </div>

    @if (session()->has('error'))
        <div class="m-4 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
            <p class="text-sm text-red-700 font-semibold">{{ session('error') }}</p>
        </div>
    @endif

    <div class="p-4 space-y-4">
        @foreach($categories as $category)
            @if($category->menus->count() > 0)
                <div x-data="{ expanded: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                    <button @click="expanded = !expanded" class="w-full px-5 py-4 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition focus:outline-none">
                        <h3 class="font-bold text-lg text-gray-800">{{ $category->name }}</h3>
                        <span class="text-gray-400 transition-transform duration-300 transform" :class="expanded ? 'rotate-180' : ''">
                            ▼
                        </span>
                    </button>

                    <div x-show="expanded" x-collapse>
                        <div class="p-4 space-y-4 border-t border-gray-100">
                            @foreach($category->menus as $menu)
                                <div class="flex gap-4 items-center">
                                    <div class="w-24 h-24 shrink-0 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 shadow-sm relative">
                                        @if($menu->image)
                                            <img src="{{ asset('storage/' . $menu->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-3xl">🍽️</div>
                                        @endif
                                    </div>

                                    <div class="flex-1 flex flex-col justify-between min-h-[6rem]">
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-base leading-tight">{{ $menu->name }}</h4>
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $menu->description }}</p>
                                        </div>
                                        <div class="flex justify-between items-end mt-2">
                                            <strong class="text-blue-600 text-sm font-bold block">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</strong>
                                            <button wire:click="openModal({{ $menu->id }})" class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full text-xs font-bold hover:bg-blue-600 hover:text-white transition">
                                                Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->last) <hr class="border-gray-100"> @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if($showModal && $selectedMenu)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-black/60 max-w-md mx-auto">
            <div class="absolute inset-0" wire:click="closeModal"></div>

            <div class="bg-white w-full rounded-t-3xl max-h-[85vh] flex flex-col relative animate-[slideUp_0.3s_ease-out]">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mt-3 mb-2"></div>

                <div class="px-5 pb-3 border-b border-gray-100 flex justify-between items-center shrink-0">
                    <h3 class="font-bold text-xl text-gray-800">{{ $selectedMenu->name }}</h3>
                    <button wire:click="closeModal" class="bg-gray-100 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center font-bold hover:bg-gray-200">&times;</button>
                </div>

                @if (session()->has('option_error'))
                    <div class="px-5 pt-4 shrink-0">
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-sm animate-pulse">
                            <span class="text-lg">⚠️</span> {{ session('option_error') }}
                        </div>
                    </div>
                @endif

                <div class="p-5 overflow-y-auto flex-1 space-y-5">
                    @foreach($selectedMenu->options as $option)
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <h4 class="font-bold text-gray-800 mb-3 flex justify-between items-center">
                                {{ $option->name }}
                                @if($option->is_required) <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-xs">Wajib</span> @endif
                            </h4>

                            <div class="space-y-3">
                                @foreach($option->items as $item)
                                    <label class="flex justify-between items-center w-full cursor-pointer">
                                        <div class="flex items-center gap-3">
                                            @if($option->max_choices == 1)
                                                <input type="radio" wire:model="selectedOptions.{{ $option->id }}" value="{{ $item->id }}" name="option_{{ $option->id }}" class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                                            @else
                                                <input type="checkbox" wire:model="selectedOptions.{{ $option->id }}.{{ $item->id }}" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                                            @endif
                                            <span class="text-gray-700 text-sm font-medium">{{ $item->name }}</span>
                                        </div>
                                        @if($item->additional_price > 0)
                                            <span class="text-gray-500 text-xs">+Rp {{ number_format($item->additional_price, 0, ',', '.') }}</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">Jumlah Pesanan</h4>
                        <div class="flex items-center gap-4">
                            <button type="button" wire:click="$set('quantity', {{ $quantity > 1 ? $quantity - 1 : 1 }})" class="w-10 h-10 rounded-full bg-gray-200 text-gray-800 font-bold text-xl flex items-center justify-center hover:bg-gray-300">-</button>
                            <span class="text-xl font-bold">{{ $quantity }}</span>
                            <button type="button" wire:click="$set('quantity', {{ $quantity + 1 }})" class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-bold text-xl flex items-center justify-center hover:bg-blue-200">+</button>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">Catatan Pesanan</h4>
                        <textarea wire:model="notes" rows="2" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 outline-none text-sm bg-gray-50" placeholder="Cth: Jangan pakai seledri, pedas sedang..."></textarea>
                    </div>
                </div>

                <div class="p-5 border-t border-gray-100 bg-white shrink-0">
                    <button wire:click="addToCart" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg transition">
                        {{ $editingCartIndex !== null ? 'Simpan Perubahan' : 'Masukkan Keranjang' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showCartModal && count($cart) > 0)
        <div class="fixed inset-0 z-40 flex items-end justify-center bg-black/60 max-w-md mx-auto">
            <div class="absolute inset-0" wire:click="toggleCart"></div>

            <div class="bg-gray-50 w-full rounded-t-3xl max-h-[80vh] flex flex-col relative animate-[slideUp_0.3s_ease-out]">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mt-3 mb-2"></div>

                <div class="px-5 pb-3 border-b border-gray-200 flex justify-between items-center shrink-0">
                    <h3 class="font-bold text-xl text-gray-800">Detail Pesanan</h3>
                    <button wire:click="toggleCart" class="text-gray-500 font-semibold hover:text-gray-800 text-sm">Tutup</button>
                </div>

                <div class="p-5 overflow-y-auto flex-1 space-y-4">
                    @foreach($cart as $index => $item)
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $item['quantity'] }}x {{ $item['name'] }}</h4>

                                    <p class="text-xs text-gray-500 mt-1">
                                        @if(isset($item['options']) && count($item['options']) > 0)
                                            <span class="font-medium text-gray-700">
                                                {{ implode(', ', array_column($item['options'], 'name')) }}
                                            </span>
                                        @endif
                                    </p>

                                    @if(!empty($item['notes']))
                                        <p class="text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded mt-1 italic">Catatan: {{ $item['notes'] }}</p>
                                    @endif
                                </div>
                                <strong class="text-gray-800 text-sm">Rp {{ number_format($item['total_price'], 0, ',', '.') }}</strong>
                            </div>

                            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-50">
                                <button wire:click="editCartItem({{ $index }})" class="flex-1 text-xs font-semibold text-blue-600 bg-blue-50 py-1.5 rounded-lg border border-blue-100">✎ Edit</button>
                                <button wire:click="removeCartItem({{ $index }})" class="flex-1 text-xs font-semibold text-red-600 bg-red-50 py-1.5 rounded-lg border border-red-100">🗑️ Hapus</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-5 bg-white border-t border-gray-200 shrink-0">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-600 font-semibold">Total Pembayaran</span>
                        <strong class="text-xl text-blue-600">Rp {{ number_format(array_sum(array_column($cart, 'total_price')), 0, ',', '.') }}</strong>
                    </div>
                    <button wire:click="submitOrder" wire:loading.attr="disabled" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg transition text-lg flex justify-center items-center gap-2 disabled:bg-green-400">
                        <span wire:loading.remove wire:target="submitOrder">Pesan Sekarang 🚀</span>
                        <span wire:loading wire:target="submitOrder">Memproses... ⏳</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(count($cart) > 0 && !$showCartModal && !$isOrderSubmitted)
        <div class="fixed bottom-5 left-1/2 transform -translate-x-1/2 w-full px-4 max-w-md z-30">
            <button wire:click="toggleCart" class="w-full bg-blue-600 text-white p-4 rounded-2xl flex justify-between items-center shadow-[0_8px_30px_rgb(0,0,0,0.2)] hover:bg-blue-700 transition focus:outline-none">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-800 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                        {{ array_sum(array_column($cart, 'quantity')) }}
                    </div>
                    <div class="text-left">
                        <span class="block text-xs text-blue-200 font-semibold">Total Pesanan</span>
                        <strong class="text-sm">Rp {{ number_format(array_sum(array_column($cart, 'total_price')), 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="font-bold text-sm bg-white text-blue-600 px-4 py-2 rounded-xl">
                    Lihat Keranjang
                </div>
            </button>
        </div>
    @endif

</div>

<style>
    @keyframes slideUp {
        from { transform: translateY(100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
