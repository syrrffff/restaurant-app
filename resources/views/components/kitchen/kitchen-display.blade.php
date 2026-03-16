<div wire:poll.5s class="p-6 relative" x-data="{ confirmOpen: @entangle('isConfirmOpen') }">

    @if (session()->has('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms
       class="fixed top-5 right-5 z-50 bg-green-500 text-white px-6 py-3 rounded shadow-lg flex items-center gap-3">
       <span class="text-xl">✅</span><strong class="font-semibold">{{ session('success') }}</strong>
   </div>
   @endif

   <div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-2">Layar Dapur (Kitchen Display)</h2>
    <p class="text-sm text-gray-500 m-0">Menampilkan item pesanan yang belum selesai dimasak.</p>
</div>

<div class="flex flex-wrap gap-6 items-start">

    @forelse($orders as $order)
    @php
    $isPending = $order->kitchen_status == 'pending';
    $borderColor = $isPending ? 'border-yellow-500' : 'border-blue-600';
    $badgeBg = $isPending ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800';
    @endphp

    <div class="bg-white w-80 rounded-xl shadow-md border-t-4 {{ $borderColor }} p-5">

        <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
            <h3 class="text-lg font-bold text-gray-800 m-0 truncate">
                {{ $order->order_type == 'takeaway' ? '🥡 ' . ($order->customer_name ?? 'Takeaway') : 'Meja ' . ($order->table->table_number ?? '?') }}
            </h3>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $badgeBg }}">
                {{ $isPending ? 'Ada Pesanan Baru' : 'Sedang Dimasak' }}
            </span>
        </div>

        <ul class="list-none p-0 m-0 mb-6">
            @foreach($order->items as $item)
            <li class="mb-4 pb-3 border-b border-dashed border-gray-200 last:border-0 last:pb-0">

                <div class="flex justify-between items-start mb-1">
                    <strong class="text-gray-800 font-semibold text-sm">{{ $item->quantity }}x {{ $item->menu->name }}</strong>

                    @if($item->item_status == 'pending')
                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded">BARU</span>
                    @elseif($item->item_status == 'cooking')
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded">DIMASAK</span>
                    @endif
                </div>

                @if($item->selectedOptions->count() > 0)
                <ul class="pl-4 text-xs text-gray-500 mt-1 list-disc">
                    @foreach($item->selectedOptions as $opt)
                    <li>{{ $opt->option_name }}</li>
                    @endforeach
                </ul>
                @endif

                @if($item->notes)
                <div class="text-red-700 text-xs mt-2 bg-red-50 px-3 py-2 rounded-md border border-red-100">
                    📝 {{ $item->notes }}
                </div>
                @endif
            </li>
            @endforeach
        </ul>

        @php
        $orderTitle = $order->order_type == 'takeaway' ? 'Pesanan ' . ($order->customer_name ?? 'Takeaway') : 'Meja ' . ($order->table->table_number ?? '?');
        @endphp

        <div class="flex gap-3">
            @if($isPending)
            <button wire:click="showConfirm('startCooking', {{ $order->id }}, 'Tandai {{ $orderTitle }} mulai dimasak?', 'warning')" class="w-full py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition shadow-sm">
                Mulai Masak
            </button>
            @elseif($order->kitchen_status == 'cooking')
            <button wire:click="showConfirm('markReady', {{ $order->id }}, 'Semua {{ $orderTitle }} sudah siap disajikan?', 'warning')" class="w-full py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold transition shadow-sm">
                Siap Disajikan
            </button>
            @endif
        </div>

    </div>
    @empty
    <div class="w-full bg-white text-center p-12 rounded-xl border border-gray-200 shadow-sm text-gray-500">
        <div class="text-5xl mb-4">👨‍🍳</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum ada antrian pesanan</h3>
        <p class="m-0">Silakan santai dulu, dapur masih sepi! ☕</p>
    </div>
    @endforelse

</div>

@include('components.confirm-modal')

</div>
