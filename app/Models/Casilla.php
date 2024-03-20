<?php

namespace App\Models;

use App\Models\AsignacionGeografica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Casilla extends Model
{
    //
    use SoftDeletes; // Opcional, si deseas utilizar borrado suave.

    protected $table = 'casillas';
    
    protected $fillable = [
        'id',
        'numero',
        'seccion_id',
        'status',
        'tipo',
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    // Si solo debe haber una asignación por zona
    public function asignacionGeografica(): MorphOne
    {
        return $this->morphOne(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }
}
