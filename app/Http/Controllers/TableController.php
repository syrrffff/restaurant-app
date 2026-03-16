<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    public function generateQr($id)
    {
        // Cari meja berdasarkan ID
        $table = Table::findOrFail($id);

        // Buat URL tujuan saat QR discan (menggunakan qr_token agar aman)
        $url = url('/scan/' . $table->qr_token);

        // Generate QR Code ukuran 300x300 pixel
        $qrCode = QrCode::size(300)->generate($url);

        // Tampilkan ke halaman Blade
        return view('admin.tables.qr', compact('table', 'qrCode', 'url'));
    }
}
