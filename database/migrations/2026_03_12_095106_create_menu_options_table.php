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
    Schema::create('menu_options', function (Blueprint $table) {
        $table->id();
        $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
        $table->string('name'); // e.g., "Tingkat Kepedasan", "Ukuran"
        $table->boolean('is_required')->default(false);
        $table->integer('max_choices')->default(1);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_options');
    }
};
