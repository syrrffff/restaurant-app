<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Mengambil semua daftar menu
     */
    public function index()
    {
        // Mengambil menu yang aktif saja, beserta kategori dan opsi variannya
        $menus = Menu::with(['category', 'options.items'])
            ->where('is_available', true)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar menu berhasil diambil',
            'data'    => $menus
        ], 200);
    }

    /**
     * Mengambil detail satu menu spesifik (misal saat diklik)
     */
    public function show($id)
    {
        $menu = Menu::with(['category', 'options.items'])->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail menu berhasil diambil',
            'data'    => $menu
        ], 200);
    }
}
