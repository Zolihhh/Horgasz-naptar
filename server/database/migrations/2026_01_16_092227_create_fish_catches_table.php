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
        Schema::create('fish_catches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specieId')->constrained('species')->onDelete('restrict');
            $table->foreignId('lureId')->constrained('lures')->onDelete('restrict');
            $table->foreignId('catchLogId')->constrained('catch_logs')->onDelete('restrict');
            $table->decimal('weight', 3, 2);
            $table->decimal('length', 5);
            $table->dateTime('catchTime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fish_catches');
    }
};
