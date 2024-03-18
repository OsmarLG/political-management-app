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
        Schema::create('encuesta_preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('texto_pregunta');
            $table->foreignId('encuesta_id')->constrained('encuestas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuesta_preguntas');
    }
};
