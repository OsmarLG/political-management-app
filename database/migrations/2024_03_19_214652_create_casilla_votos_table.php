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
        Schema::create('casilla_votos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_votos')->require();
            $table->year('ano')->require();
            $table->foreignId('casilla_id')->constrained('casillas')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes(); // Esto agrega la columna `deleted_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casilla_votos');
    }
};
