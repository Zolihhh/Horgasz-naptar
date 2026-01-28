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
        $table->foreignId('locationid')->constrained('locations')->onDelete('cascade');
        $table->foreignId('userid')->constrained('users')->onDelete('cascade');
        $table->text('comment')->nullable();
        $table->date('fishing_start')->nullable();
        $table->date('fishing_end')->nullable();
        $table->timestamps();
        $table->unique(['userid', 'locationid','fishing_start','fishing_end']);
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
