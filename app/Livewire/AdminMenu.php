<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Menu;
use App\Models\Category;
use App\Models\MenuOption;
use App\Models\MenuOptionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\WithConfirmation;

class AdminMenu extends Component
{
    use WithPagination, WithFileUploads, WithConfirmation;

    public $search = '';

    // --- KONTROL TAB ---
    public $activeTab = 'menu'; // Default tab yang terbuka

    // --- KONTROL MODAL MENU ---
    public $isModalOpen = false;
    public $menu_id, $name, $description, $base_price, $category_id;
    public $is_draft = false;
    public $image, $old_image;
    public $options = [];

    // --- KONTROL MODAL KATEGORI ---
    public $isCategoryModalOpen = false;
    public $cat_id, $cat_name;
    public $cat_is_active = true;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- FUNGSI PINDAH TAB ---
    public function switchTab($tabName)
    {
        $this->activeTab = $tabName;
        $this->search = ''; // Reset pencarian saat ganti tab
        $this->resetPage();
    }

    // =======================================================
    // FUNGSI CRUD KATEGORI
    // =======================================================
    public function openCategoryModal()
    {
        $this->resetCategoryForm();
        $this->isCategoryModalOpen = true;
    }

    public function closeCategoryModal()
    {
        $this->isCategoryModalOpen = false;
        $this->resetCategoryForm();
    }

    public function resetCategoryForm()
    {
        $this->reset(['cat_id', 'cat_name']);
        $this->cat_is_active = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'cat_name' => 'required|string|max:255',
        ]);

        Category::updateOrCreate(
            ['id' => $this->cat_id],
            [
                'name' => $this->cat_name,
                'is_active' => $this->cat_is_active,
            ]
        );

        session()->flash('success', $this->cat_id ? 'Kategori berhasil diperbarui!' : 'Kategori baru berhasil ditambahkan!');
        $this->closeCategoryModal();
    }

    public function editCategory($id)
    {
        $cat = Category::find($id);
        if ($cat) {
            $this->cat_id = $cat->id;
            $this->cat_name = $cat->name;
            $this->cat_is_active = $cat->is_active;

            $this->isCategoryModalOpen = true;
        }
    }

    public function deleteCategory($id)
    {
        $cat = Category::find($id);
        if ($cat) {
            // Catatan: Jika Anda menggunakan foreign key constraint,
            // pastikan menu dengan kategori ini juga ditangani (misal set null atau hapus)
            $cat->delete();
            session()->flash('success', 'Kategori berhasil dihapus!');
        }
    }

    public function toggleCategoryStatus($id)
    {
        $cat = Category::find($id);
        if ($cat) {
            $cat->update(['is_active' => !$cat->is_active]);
        }
    }


    // =======================================================
    // FUNGSI CRUD MENU (Tetap Sama Seperti Sebelumnya)
    // =======================================================
    public function openModal() { $this->resetForm(); $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetForm(); }
    public function addOption() { $this->options[] = ['name' => '', 'is_required' => false, 'max_choices' => 1, 'items' => []]; }
    public function removeOption($index) { unset($this->options[$index]); $this->options = array_values($this->options); }
    public function addOptionItem($optionIndex) { $this->options[$optionIndex]['items'][] = ['name' => '', 'additional_price' => 0]; }
    public function removeOptionItem($optionIndex, $itemIndex) { unset($this->options[$optionIndex]['items'][$itemIndex]); $this->options[$optionIndex]['items'] = array_values($this->options[$optionIndex]['items']); }

    public function saveMenu()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'options.*.name' => 'required|string',
            'options.*.items.*.name' => 'required|string',
            'options.*.items.*.additional_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $imagePath = $this->old_image;
            if ($this->image) {
                if ($this->old_image) Storage::disk('public')->delete($this->old_image);
                $imagePath = $this->image->store('menus', 'public');
            }

            $menu = Menu::updateOrCreate(
                ['id' => $this->menu_id],
                [
                    'name' => $this->name,
                    'description' => $this->description,
                    'base_price' => $this->base_price,
                    'category_id' => $this->category_id,
                    'image' => $imagePath,
                    'is_available' => !$this->is_draft,
                ]
            );

            if ($this->menu_id) MenuOption::where('menu_id', $menu->id)->delete();

            foreach ($this->options as $opt) {
                $menuOption = MenuOption::create([
                    'menu_id' => $menu->id,
                    'name' => $opt['name'],
                    'is_required' => $opt['is_required'] ? true : false,
                    'max_choices' => $opt['max_choices'] ?: 1,
                ]);

                if (!empty($opt['items'])) {
                    foreach ($opt['items'] as $item) {
                        MenuOptionItem::create([
                            'menu_option_id' => $menuOption->id,
                            'name' => $item['name'],
                            'additional_price' => $item['additional_price'] ?: 0,
                        ]);
                    }
                }
            }

            DB::commit();
            session()->flash('success', $this->menu_id ? 'Menu diperbarui!' : 'Menu ditambahkan!');
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function editMenu($id)
    {
        $menu = Menu::with('options.items')->find($id);
        if ($menu) {
            $this->menu_id = $menu->id;
            $this->name = $menu->name;
            $this->description = $menu->description;
            $this->base_price = $menu->base_price;
            $this->category_id = $menu->category_id;
            $this->old_image = $menu->image;
            $this->is_draft = !$menu->is_available;

            $this->options = [];
            foreach ($menu->options as $opt) {
                $items = [];
                foreach ($opt->items as $item) {
                    $items[] = ['name' => $item->name, 'additional_price' => $item->additional_price];
                }
                $this->options[] = ['name' => $opt->name, 'is_required' => $opt->is_required, 'max_choices' => $opt->max_choices, 'items' => $items];
            }
            $this->isModalOpen = true;
        }
    }

    public function deleteMenu($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            if ($menu->image) Storage::disk('public')->delete($menu->image);
            $menu->delete();
            session()->flash('success', 'Menu berhasil dihapus!');
        }
    }

    public function toggleAvailability($id)
    {
        $menu = Menu::find($id);
        if ($menu) $menu->update(['is_available' => !$menu->is_available]);
    }

    public function resetForm()
    {
        $this->reset(['menu_id', 'name', 'description', 'base_price', 'category_id', 'options', 'image', 'old_image', 'is_draft']);
    }

    public function render()
    {
        // Panggil data berdasarkan tab yang aktif agar lebih ringan
        if ($this->activeTab == 'menu') {
            $menus = Menu::with('category')->where('name', 'like', '%' . $this->search . '%')->latest()->paginate(10);
            $categoriesList = Category::where('is_active', true)->get();
            $paginatedCategories = null;
        } else {
            $menus = null;
            $categoriesList = null;
            $paginatedCategories = Category::where('name', 'like', '%' . $this->search . '%')->latest()->paginate(10);
        }

        return view('components.admin.admin-menu', compact('menus', 'categoriesList', 'paginatedCategories'))
            ->layout('components.layouts.app');
    }
}
