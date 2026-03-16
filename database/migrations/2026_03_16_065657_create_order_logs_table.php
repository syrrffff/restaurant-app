<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('order_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->string('action'); // 'created', 'edited', 'deleted', 'reprinted'
        $table->text('description'); // Penjelasan detail aktivitas
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_logs');
    }
};
