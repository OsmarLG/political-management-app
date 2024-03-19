<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncuestaPregunta extends Model
{
    use HasFactory;

    protected $fillable = [
        'texto_pregunta',
        'encuesta_id'
    ];

    public function opciones(){
        return $this->hasMany(EncuestaOpcion::class,'pregunta_id','id');
    }
}
