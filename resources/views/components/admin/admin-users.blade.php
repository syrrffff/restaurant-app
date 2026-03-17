<div class="p-6 relative" x-data="{ modalOpen: @entangle('isModalOpen'), confirmOpen: @entangle('isConfirmOpen') }">

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms
             class="fixed top-5 right-5 z-[100] bg-green-500 text-white px-6 py-3 rounded shadow-lg flex items-center gap-3">
            <span class="text-xl">✅</span><strong class="font-semibold">{{ session('success') }}</strong>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.duration.500ms
             class="fixed top-5 right-5 z-[100] bg-red-500 text-white px-6 py-3 rounded shadow-lg flex items-center gap-3">
            <span class="text-xl">⚠️</span><strong class="font-semibold">{{ session('error') }}</strong>
        </div>
    @endif

    <div class="mb-8 border-b border-gray-200 pb-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Manajemen Pengguna (Users)</h2>
        <p class="text-sm text-gray-500 m-0">Kelola akun akses untuk Kasir, Dapur (Kitchen), dan Admin.</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 w-full">

        <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-100 pb-4 mb-5 gap-4">
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none text-sm">
                <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button wire:click="editMyProfile" class="w-full md:w-auto px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 rounded-lg font-bold transition shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-pen"></i> Profil Saya
                </button>
                <button wire:click="openModal" class="w-full md:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition shadow-md flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus"></i> Tambah Pengguna
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse mb-4 whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-y border-gray-200">
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Nama Lengkap</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">Email Akses</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-center">Role / Jabatan</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-4">
                                <strong class="text-gray-800 flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    {{ $user->name }}
                                </strong>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-purple-100 text-purple-700',
                                        'cashier' => 'bg-green-100 text-green-700',
                                        'kitchen' => 'bg-orange-100 text-orange-700'
                                    ];
                                    $roleIcons = [
                                        'admin' => 'fa-user-tie',
                                        'cashier' => 'fa-cash-register',
                                        'kitchen' => 'fa-fire-burner'
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-xs font-bold rounded-full capitalize {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                                    <i class="fa-solid {{ $roleIcons[$user->role] ?? 'fa-user' }} mr-1"></i> {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="editUser({{ $user->id }})" class="px-3 py-1.5 text-xs font-bold rounded bg-yellow-400 hover:bg-yellow-500 text-white transition mr-1 w-16 shadow-sm">
                                    Edit
                                </button>
                                <button wire:click="showConfirm('deleteUser', {{ $user->id }}, 'Yakin ingin menghapus akun {{ $user->name }} secara permanen?', 'danger')" class="px-3 py-1.5 text-xs font-bold rounded bg-red-500 hover:bg-red-600 text-white transition shadow-sm">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-500">Tidak ada pengguna lain ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>

    <div x-show="modalOpen" style="display: none;" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @click.self="modalOpen = false; $wire.closeModal()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col overflow-hidden">

            <div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800">
                    {{ $isEditingSelf ? 'Edit Profil Saya' : ($user_id ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru') }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <form wire:submit.prevent="saveUser" id="userForm" class="flex flex-col gap-4">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" wire:model="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none text-sm">
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email Akses *</label>
                        <input type="email" wire:model="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none text-sm">
                        @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Role / Jabatan *</label>
                        <select wire:model="role" required {{ $isEditingSelf ? 'disabled' : '' }} class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-blue-500 text-sm {{ $isEditingSelf ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white' }}">
                            <option value="">-- Pilih Akses --</option>
                            <option value="cashier">Cashier (Kasir & POS)</option>
                            <option value="kitchen">Kitchen (Dapur)</option>
                            <option value="admin">Admin (Pemilik/Manajer)</option>
                        </select>
                        @if($isEditingSelf)
                            <span class="text-[10px] text-gray-500 font-semibold mt-1 block">🔒 Anda tidak dapat mengubah role Anda sendiri.</span>
                        @endif
                        @error('role') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Password Login {{ $user_id ? '(Kosongkan jika tidak ingin diubah)' : '*' }}
                        </label>
                        <input type="password" wire:model="password" {{ $user_id ? '' : 'required' }} placeholder="Minimal 6 karakter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none text-sm">
                        @error('password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                </form>
            </div>

            <div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                <button type="button" wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-semibold transition text-sm">Batal</button>
                <button type="submit" form="userForm" wire:loading.attr="disabled" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg font-bold transition shadow-md flex items-center gap-2 text-sm">
                    <span wire:loading.remove wire:target="saveUser">💾 Simpan</span>
                    <span wire:loading wire:target="saveUser">⏳ Menyimpan...</span>
                </button>
            </div>

        </div>
    </div>

    @include('components.confirm-modal')
</div>
