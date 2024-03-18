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
        Schema::create('encuesta_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained('encuestas')->onDelete('cascade');
            $table->foreignId('pregunta_id')->constrained('encuesta_preguntas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('asignacion_geografica_id')->constrained('asignaciones_geograficas')->onDelete('cascade');
            $table->string('folio');
            $table->string('respuesta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuesta_respuestas');
    }
};
