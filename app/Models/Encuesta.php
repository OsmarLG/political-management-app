<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    use HasFactory;
    protected $fillable = [
        'titulo',
    ];

    public function preguntas(){
        return $this->hasMany(EncuestaPregunta::class);
    }
    public function opciones(){
        return $this->hasMany(EncuestaOpcion::class);
    }
}
