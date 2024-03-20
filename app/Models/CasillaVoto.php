<?php

namespace App\Models;

use App\Models\Casilla;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CasillaVoto extends Model
{
    use HasFactory;

     //
     use SoftDeletes; // Opcional, si deseas utilizar borrado suave.

     protected $table = 'casilla_votos';
     
     protected $fillable = [
        'id',
        'numero_votos',
        'ano',
        'casilla_id',
     ];
 
     public function casilla()
     {
         return $this->belongsTo(Casilla::class, 'casilla_id');
     }
}
