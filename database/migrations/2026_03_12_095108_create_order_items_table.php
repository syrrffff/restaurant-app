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
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        $table->foreignId('menu_id')->constrained()->restrictOnDelete();
        $table->integer('quantity');
        $table->integer('base_price');
        $table->integer('total_price'); // (base_price + tambahan varian) * quantity
        $table->string('notes')->nullable();
        $table->enum('item_status', ['pending', 'cooking', 'done'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
