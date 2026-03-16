<div class="p-6 relative" x-data="{ modalOpen: @entangle('isModalOpen'), confirmOpen: @entangle('isConfirmOpen'), catModalOpen: @entangle('isCategoryModalOpen') }">

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.duration.500ms
             class="fixed top-5 right-5 z-[100] bg-green-500 text-white px-6 py-3 rounded shadow-lg flex items-center gap-3">
            <span class="text-xl">✅</span><strong class="font-semibold">{{ session('success') }}</strong>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="fixed top-5 right-5 z-[100] bg-red-500 text-white px-6 py-3 rounded shadow-lg">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Admin Master Data</h2>
        <p class="text-sm text-gray-500 m-0">Kelola daftar Menu dan Kategori Makanan.</p>
    </div>

    <div class="flex space-x-6 mb-6 border-b border-gray-200">
        <button wire:click="switchTab('menu')" class="pb-3 text-sm font-bold transition-colors border-b-2 {{ $activeTab == 'menu' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
            🍽️ Daftar Menu
        </button>
        <button wire:click="switchTab('category')" class="pb-3 text-sm font-bold transition-colors border-b-2 {{ $activeTab == 'category' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
            📑 Kategori Menu
        </button>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 w-full">

        <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-100 pb-4 mb-5 gap-4">
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari {{ $activeTab == 'menu' ? 'nama menu' : 'nama kategori' }}..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none text-sm">
                <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
            </div>

            @if($activeTab == 'menu')
                <button wire:click="openModal" wire:loading.attr="disabled" class="w-full md:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg font-bold transition shadow-md flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="openModal">➕ Tambah Menu</span>
                    <span wire:loading wire:target="openModal">⏳ Membuka...</span>
                </button>
            @else
                <button wire:click="openCategoryModal" wire:loading.attr="disabled" class="w-full md:w-auto px-5 py-2.5 bg-gray-800 hover:bg-gray-900 disabled:bg-gray-500 text-white rounded-lg font-bold transition shadow-md flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="openCategoryModal">➕ Tambah Kategori</span>
                    <span wire:loading wire:target="openCategoryModal">⏳ Membuka...</span>
                </button>
            @endif
        </div>

        @if($activeTab == 'menu')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse mb-4 whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-200">
                            <th class="px-4 py-3 text-sm font-semibold text-gray-600 w-16">Foto</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-600">Menu</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-600">Kategori</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-600">Harga Dasar</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-center">Status</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $menu)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="Foto" class="w-12 h-12 object-cover rounded-md shadow-sm border border-gray-200">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-md flex items-center justify-center text-gray-400 text-xs">No Pic</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <strong class="text-gray-800 block">{{ $menu->name }}</strong>
                                    <small class="text-gray-500 truncate w-48 inline-block">{{ $menu->description }}</small>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $menu->category->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-red-600 font-bold">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="toggleAvailability({{ $menu->id }})" class="px-3 py-1 text-xs font-bold rounded-full transition {{ $menu->is_available ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                        {{ $menu->is_available ? 'Tersedia' : 'Habis / Draft' }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="editMenu({{ $menu->id }})" wire:loading.attr="disabled" class="px-3 py-1.5 text-xs font-bold rounded bg-yellow-400 hover:bg-yellow-500 disabled:bg-yellow-300 text-white transition mr-1 w-16">
                                        <span wire:loading.remove wire:target="editMenu({{ $menu->id }})">Edit</span>
                                        <span wire:loading wire:target="editMenu({{ $menu->id }})">⏳...</span>
                                    </button>
                                    <button wire:click="showConfirm('deleteMenu', {{ $menu->id }}, 'Yakin ingin menghapus menu ini beserta semua opsi variannya?', 'danger')" class="px-3 py-1.5 text-xs font-bold rounded bg-red-500 hover:bg-red-600 text-white transition">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Menu tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $menus->links() }}</div>
            </div>
        @endif

        @if($activeTab == 'category')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse mb-4 whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-200">
                            <th class="px-4 py-3 text-sm font-semibold text-gray-600">Nama Kategori</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-center">Status</th>
                            <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedCategories as $cat)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <strong class="text-gray-800">{{ $cat->name }}</strong>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="toggleCategoryStatus({{ $cat->id }})" class="px-3 py-1 text-xs font-bold rounded-full transition {{ $cat->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                        {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="editCategory({{ $cat->id }})" class="px-3 py-1.5 text-xs font-bold rounded bg-yellow-400 hover:bg-yellow-500 text-white transition mr-1">
                                        Edit
                                    </button>
                                    <button wire:click="showConfirm('deleteCategory', {{ $cat->id }}, 'Yakin ingin menghapus kategori ini? Pastikan tidak ada menu yang terkait dengannya.', 'danger')" class="px-3 py-1.5 text-xs font-bold rounded bg-red-500 hover:bg-red-600 text-white transition">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-10 text-center text-gray-500">Kategori tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $paginatedCategories->links() }}</div>
            </div>
        @endif

    </div>

    <div x-show="modalOpen" style="display: none;" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 sm:p-6">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden" @click.outside="modalOpen = false; $wire.closeModal()">
            <div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800">{{ $menu_id ? 'Edit Data Menu' : 'Tambah Menu Baru' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 overscroll-contain" style="transform: translateZ(0); -webkit-overflow-scrolling: touch;">
                <form wire:submit.prevent="saveMenu" id="menuForm" class="flex flex-col gap-5">

                    <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg flex items-center gap-2">
                        <input type="checkbox" wire:model="is_draft" id="draft" class="w-5 h-5 text-yellow-600 rounded">
                        <label for="draft" class="text-sm font-semibold text-yellow-800 cursor-pointer">Simpan sebagai Draft (Sembunyikan dari aplikasi pelanggan)</label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Menu *</label>
                                <input type="text" wire:model="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori *</label>
                                <select wire:model="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white outline-none">
                                    <option value="">-- Pilih Kategori --</option>
                                    @if($categoriesList)
                                        @foreach($categoriesList as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Harga Dasar (Rp) *</label>
                                <input type="number" wire:model="base_price" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Singkat</label>
                                <textarea wire:model="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Produk (Opsional)</label>
                                <input type="file" wire:model="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <div wire:loading wire:target="image" class="text-xs text-blue-500 mt-1">Mengunggah foto...</div>
                                @if ($image) <img src="{{ $image->temporaryUrl() }}" class="mt-2 w-24 h-24 object-cover rounded-lg border border-gray-200">
                                @elseif ($old_image) <img src="{{ asset('storage/' . $old_image) }}" class="mt-2 w-24 h-24 object-cover rounded-lg border border-gray-200">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-gray-200 pt-5">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <label class="block text-base font-bold text-gray-800">Varian & Opsi Tambahan</label>
                                <p class="text-xs text-gray-500">Atur level pedas, topping, dll.</p>
                            </div>
                            <button type="button" wire:click="addOption" wire:loading.attr="disabled" class="text-sm bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition shadow-sm">
                                <span wire:loading.remove wire:target="addOption">+ Tambah Opsi</span>
                                <span wire:loading wire:target="addOption">⏳ Menambahkan...</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            @foreach($options as $optIndex => $option)
                                <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 relative shadow-sm">
                                    <button type="button" wire:click="removeOption({{ $optIndex }})" class="absolute top-3 right-3 text-red-500 hover:text-red-700 font-bold bg-red-50 rounded-full w-8 h-8 flex items-center justify-center">&times;</button>
                                    <div class="flex flex-col md:flex-row gap-4 mb-4 pr-10">
                                        <div class="flex-1">
                                            <input type="text" wire:model="options.{{ $optIndex }}.name" placeholder="Nama Kelompok (Cth: Ekstra Topping)" class="w-full px-3 py-2 text-sm border border-gray-300 rounded outline-none font-semibold" required>
                                        </div>
                                        <div class="flex gap-4 items-center text-sm bg-white px-3 py-2 border border-gray-300 rounded">
                                            <label class="flex items-center gap-2 cursor-pointer border-r border-gray-200 pr-4">
                                                <input type="checkbox" wire:model="options.{{ $optIndex }}.is_required" class="w-4 h-4 text-blue-600 rounded"> Wajib?
                                            </label>
                                            <label class="flex items-center gap-2">
                                                Maksimal Pilih: <input type="number" wire:model="options.{{ $optIndex }}.max_choices" min="1" class="w-16 px-2 py-1 border border-gray-300 rounded text-center outline-none">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="pl-4 border-l-4 border-blue-300 space-y-2">
                                        @foreach($option['items'] as $itemIndex => $item)
                                            <div class="flex gap-2 items-center">
                                                <input type="text" wire:model="options.{{ $optIndex }}.items.{{ $itemIndex }}.name" placeholder="Pilihan" class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded outline-none" required>
                                                <div class="relative w-32">
                                                    <span class="absolute left-2 top-1.5 text-xs text-gray-500">Rp</span>
                                                    <input type="number" wire:model="options.{{ $optIndex }}.items.{{ $itemIndex }}.additional_price" placeholder="0" class="w-full pl-7 pr-2 py-1.5 text-sm border border-gray-300 rounded outline-none" required>
                                                </div>
                                                <button type="button" wire:click="removeOptionItem({{ $optIndex }}, {{ $itemIndex }})" class="text-red-500 hover:text-red-700 font-bold px-2 py-1 bg-white border border-red-200 rounded">X</button>
                                            </div>
                                        @endforeach
                                        <button type="button" wire:click="addOptionItem({{ $optIndex }})" class="text-sm text-blue-600 hover:text-blue-800 font-semibold mt-2 px-3 py-1.5 bg-blue-50 rounded-md border border-blue-100 transition">
                                            + Tambah Baris Pilihan
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                <button type="button" wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-semibold transition">Batal</button>
                <button type="submit" form="menuForm" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition shadow-md">Simpan Menu</button>
            </div>
        </div>
    </div>

    <div x-show="catModalOpen" style="display: none;" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm flex flex-col overflow-hidden" @click.outside="catModalOpen = false; $wire.closeCategoryModal()">
            <div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800">{{ $cat_id ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
                <button wire:click="closeCategoryModal" class="text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="saveCategory" id="catForm" class="flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kategori *</label>
                        <input type="text" wire:model="cat_name" placeholder="Cth: Makanan Utama, Minuman" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none">
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <input type="checkbox" wire:model="cat_is_active" id="cat_active" class="w-5 h-5 text-blue-600 rounded">
                        <label for="cat_active" class="text-sm font-semibold text-gray-800 cursor-pointer">Kategori Aktif (Ditampilkan ke pelanggan)</label>
                    </div>
                </form>
            </div>
            <div class="p-5 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                <button type="button" wire:click="closeCategoryModal" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-semibold transition">Batal</button>
                <button type="submit" form="catForm" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-bold transition shadow-md">Simpan</button>
            </div>
        </div>
    </div>

    @include('components.confirm-modal')

</div>
