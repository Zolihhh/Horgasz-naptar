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
        Schema::create('catch_logs', function (Blueprint $table) {
             $table->id();
        $table->unsignedBigInteger('userid');
        $table->foreignId('FishingLakeId')->constrained('helyszinek');
        $table->text('comment')->nullable();
        $table->date('fishing_start')->nullable();
        $table->date('fishing_end')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catch_logs');
    }
};
