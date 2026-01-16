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
         Schema::create('fogasi_naplok', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('userid');
        $table->foreignId('horgaszto_id')->constrained('helyszinek');
        $table->text('megjegyzes')->nullable();
        $table->date('horgaszat_kezdete')->nullable();
        $table->date('horgaszat_vege')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fogasi_naplok');
    }
};
