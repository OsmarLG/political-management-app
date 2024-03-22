<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'encuesta_id',
        'user_id',
        'manzana_id',
        'folio',
    ];

    // Si solo debe haber una asignación por zona
    public function asignacionGeografica()
    {
        return $this->morphOne(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }

    public function manzana(){
        return $this->hasOne(Manzana::class, 'id','manzana_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
