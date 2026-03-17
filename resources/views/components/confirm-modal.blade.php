<div x-show="confirmOpen" style="display: none;" x-transition.opacity
     class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm flex flex-col overflow-hidden text-center"
         @click.outside="confirmOpen = false; $wire.closeConfirm()">

        <div class="p-6 pt-8">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl"
                 :class="$wire.confirmTheme === 'danger' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600'">
                ⚠️
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Aksi</h3>

            <p class="text-sm text-gray-600 mb-8">{{ $confirmMessage }}</p>

            <div class="flex justify-center gap-3">
                <button type="button" wire:click="closeConfirm" class="px-5 py-2 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition w-full">
                    Batal
                </button>

                <button type="button" wire:click="executeConfirm" wire:loading.attr="disabled"
                        class="px-5 py-2 rounded-lg font-bold transition shadow-md w-full text-white"
                        :class="$wire.confirmTheme === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'">
                    <span wire:loading.remove wire:target="executeConfirm">Ya, Lanjutkan</span>
                    <span wire:loading wire:target="executeConfirm">⏳ Proses...</span>
                </button>
            </div>
        </div>

    </div>
</div>
