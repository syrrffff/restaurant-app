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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('table_id')->constrained()->restrictOnDelete();
        $table->string('invoice_number')->unique();
        $table->integer('subtotal')->default(0);
        $table->integer('tax_amount')->default(0);
        $table->integer('service_charge')->default(0);
        $table->integer('total_amount')->default(0);
        $table->enum('kitchen_status', ['pending', 'cooking', 'ready', 'served'])->default('pending');
        $table->enum('payment_status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
        $table->string('payment_method')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
