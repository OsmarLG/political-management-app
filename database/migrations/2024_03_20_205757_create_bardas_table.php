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
        Schema::create('bardas', function (Blueprint $table) {
            $table->id();
            $table->string('identificador')->require();
            $table->foreignId('seccion_id')->constrained('secciones')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes(); // Esto agrega la columna `deleted_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bardas');
    }
};
