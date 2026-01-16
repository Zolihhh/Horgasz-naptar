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
            $table->foreignId('spiecesID');
            $table->decimal('weight', 3, 2);
            $table->decimal('length', 3, 2);
            $table->foreignId('LureId');
            $table->dateTime('catchTime');
            $table->foreignId('catchLogId');
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
