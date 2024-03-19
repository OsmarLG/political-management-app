<?php

namespace App\Models;

use App\Models\Encuesta;
use App\Models\EncuestaPregunta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EncuestaRespuesta extends Model
{
    use HasFactory;
    protected $fillable = [
        "encuesta_id",
        "pregunta_id",
        "user_id",
        "asignacion_geografica_id",
        "folio",
        "respuesta",
    ];

    public function encuesta(){
        return $this->belongsTo(Encuesta::class, 'encuesta_id');
    }

    public function pregunta(){
        return $this->belongsTo(EncuestaPregunta::class, 'pregunta_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asignacion_geografica(){
        return $this->hasOne(AsignacionGeografica::class, 'asignacion_geografica_id');
    }
}
