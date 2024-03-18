<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncuestaOpcion extends Model
{
    use HasFactory;
    protected $fillable = [
        'pregunta_id',
        'texto_opcion',
        'encuesta_id'
    ];

    public function pregunta(){
        return $this->belongsTo(EncuestaPregunta::class, 'pregunta_id');
    }
}
