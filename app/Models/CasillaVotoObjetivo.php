<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CasillaVotoObjetivo extends Model
{
    use HasFactory;

    use SoftDeletes; // Opcional, si deseas utilizar borrado suave.

     protected $table = 'casilla_votos_objetivos';
     
     protected $fillable = [
        'id',
        'numero_votos_objetivos',
        'ano',
        'casilla_id',
     ];
 
     public function casilla()
     {
         return $this->belongsTo(Casilla::class, 'casilla_id');
     }
}
