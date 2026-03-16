<div class="p-6 relative" x-data="{ modalOpen: @entangle('isModalOpen'), confirmOpen: @entangle('isConfirmOpen') }">

    @if (session()->has('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms
     class="fixed top-5 right-5 z-[100] bg-green-500 text-white px-6 py-3 rounded shadow-lg flex items-center gap-3">
     <span class="text-xl">✅</span><strong class="font-semibold">{{ session('success') }}</strong>
 </div>
 @endif

 <div class="mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-2xl font-bold text-gray-800 mb-1">Manajemen Meja & QR Code</h2>
    <p class="text-sm text-gray-500 m-0">Kelola ketersediaan meja dan cetak QR Code untuk discan pelanggan.</p>
</div>

<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 w-full">

    <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-100 pb-4 mb-5 gap-4">
        <div class="relative w-full md:w-80">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor meja..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none text-sm">
            <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
        </div>

        <button wire:click="openModal" class="w-full md:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition duration-200 shadow-md flex items-center justify-center gap-2">
            ➕ Tambah Meja
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse mb-4 whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 border-y border-gray-200">
                    <th class="px-4 py-3 text-sm font-semibold text-gray-600">Nomor Meja</th>
                    <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-center">Status Meja</th>
                    <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-center">QR Token</th>
                    <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">Aksi & Cetak</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tables as $table)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <strong class="text-gray-800 text-lg">{{ $table->table_number }}</strong>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $table->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $table->status == 'available' ? 'Tersedia' : 'Terisi (Occupied)' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <code class="bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200 text-sm">
                            {{ $table->qr_token }}
                        </code>
                        <button wire:click="showConfirm('regenerateToken', {{ $table->id }}, 'Yakin ingin mengganti QR Token? QR lama tidak akan berlaku.', 'warning')" class="ml-2 text-blue-500 hover:text-blue-700 text-xs underline focus:outline-none">
                            Refresh Token
                        </button>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2 items-center">
                            <button wire:click="editTable({{ $table->id }})" class="px-3 py-1.5 text-xs font-bold rounded bg-yellow-400 hover:bg-yellow-500 text-white transition">Edit</button>

                            <button wire:click="showConfirm('deleteTable', {{ $table->id }}, 'Yakin ingin menghapus meja ini?', 'danger')" class="px-3 py-1.5 text-xs font-bold rounded bg-red-500 hover:bg-red-600 text-white transition">
                                Hapus
                            </button>

                            <a href="/admin/tables/{{ $table->id }}/qr" target="_blank" class="px-3 py-1.5 text-xs font-bold rounded bg-gray-800 hover:bg-gray-900 text-white transition flex items-center gap-1 shadow-sm">
                                🖨️ Cetak QR
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-gray-500">Data meja belum ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $tables->links() }}</div>
    </div>
</div>

<div x-show="modalOpen" style="display: none;" x-transition.opacity
class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4 sm:p-6">

<div class="bg-white rounded-xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden"
@click.outside="modalOpen = false; $wire.closeModal()">

<div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gray-50">
    <h3 class="text-xl font-bold text-gray-800">
        {{ $table_id ? 'Edit Meja' : 'Tambah Meja Baru' }}
    </h3>
    <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
</div>

<div class="p-6">
    <form wire:submit.prevent="saveTable" id="tableForm" class="flex flex-col gap-4">

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor / Nama Meja *</label>
            <input type="text" wire:model="table_number" placeholder="Contoh: Meja 01, VIP 1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none">
            @error('table_number') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status Saat Ini *</label>
            <select wire:model="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white outline-none">
                <option value="available">Tersedia (Available)</option>
                <option value="occupied">Terisi (Occupied)</option>
            </select>
        </div>

        @if(!$table_id)
        <div class="bg-blue-50 text-blue-700 p-3 rounded-lg text-xs border border-blue-100 mt-2">
            ℹ️ QR Token unik akan otomatis dibuat setelah Anda menyimpan meja ini.
        </div>
        @endif

    </form>
</div>

<div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
    <button type="button" wire:click="closeModal" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-semibold transition">Batal</button>
    <button type="submit" form="tableForm" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition shadow-md">
        Simpan Meja
    </button>
</div>

</div>
</div>

@include('components.confirm-modal')
</div>
