<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('order_item_options', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
        $table->foreignId('menu_option_item_id')->nullable()->constrained()->nullOnDelete();
        $table->string('option_name'); // Simpan nama varian sebagai histori statis
        $table->integer('additional_price')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
    }
};
