<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Table;
use App\Models\Category;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Meja
        Table::create([
            'table_number' => 'Meja 01',
            'qr_token' => Str::uuid(),
            'status' => 'available'
        ]);

        // 2. Buat Kategori
        $category = Category::create([
            'name' => 'Kopi & Minuman',
            'is_active' => true
        ]);

        // 3. Buat Menu
        $menu = $category->menus()->create([
            'name' => 'Manual Brew (V60)',
            'description' => 'Kopi seduh manual menggunakan metode V60 pour-over.',
            'base_price' => 25000,
            'is_available' => true
        ]);

        // 4. Buat Varian/Opsi 1: Pilihan Biji Kopi (Wajib Pilih 1)
        $beanOption = $menu->options()->create([
            'name' => 'Pilihan Biji Kopi',
            'is_required' => true,
            'max_choices' => 1
        ]);

        // Isi item untuk Pilihan Biji Kopi
        $beanOption->items()->createMany([
            ['name' => 'Arabica Gayo', 'additional_price' => 0],
            ['name' => 'Robusta Temanggung', 'additional_price' => 0],
            ['name' => 'Ethiopia Yirgacheffe', 'additional_price' => 5000], // Ada tambahan harga
        ]);

        // 5. Buat Varian/Opsi 2: Suhu Penyajian (Wajib Pilih 1)
        $tempOption = $menu->options()->create([
            'name' => 'Suhu Penyajian',
            'is_required' => true,
            'max_choices' => 1
        ]);

        // Isi item untuk Suhu Penyajian
        $tempOption->items()->createMany([
            ['name' => 'Panas (Hot)', 'additional_price' => 0],
            ['name' => 'Dingin (Ice)', 'additional_price' => 3000], // Es tambah 3 ribu
        ]);

        // 6. Buat Varian/Opsi 3: Ekstra (Opsional, Boleh pilih lebih dari 1)
        $extraOption = $menu->options()->create([
            'name' => 'Tambahan (Ekstra)',
            'is_required' => false,
            'max_choices' => 2
        ]);

        $extraOption->items()->createMany([
            ['name' => 'Ekstra Shot Espresso', 'additional_price' => 8000],
            ['name' => 'Gula Aren', 'additional_price' => 3000],
        ]);
    }
}
