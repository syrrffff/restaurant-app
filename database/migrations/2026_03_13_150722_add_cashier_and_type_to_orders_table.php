<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Ubah table_id agar boleh kosong (untuk takeaway)
            $table->unsignedBigInteger('table_id')->nullable()->change();

            // Tambahkan tipe pesanan (Dine-in / Takeaway)
            $table->string('order_type')->default('dine_in')->after('table_id');

            // Tambahkan id kasir yang melayani
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete()->after('payment_method');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cashier_id']);
            $table->dropColumn(['cashier_id', 'order_type']);
            // Catatan: Mengembalikan table_id menjadi tidak nullable bisa menyebabkan error jika ada data null
        });
    }
};
