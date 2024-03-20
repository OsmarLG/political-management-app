<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    public const CANDIDATO = 'SIN DEFINIR';

    public static function getCandidato() {
        return self::CANDIDATO;
    }

}
