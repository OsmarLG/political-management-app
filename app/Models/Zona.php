<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Zona extends Model
{
    use HasFactory;
    use SoftDeletes; // Opcional, si deseas utilizar borrado suave.

    protected $table = 'zonas';
    
    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'status',
    ];

    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'zona_id');
    }

    public function manzanas()
    {
        return $this->hasManyThrough(Manzana::class, Seccion::class);
    }

    public function asignacionesGeograficas(): MorphMany
    {
        return $this->morphMany(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }

    // Si solo debe haber una asignación por zona
    public function asignacionGeografica(): MorphOne
    {
        return $this->morphOne(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }

    public function getIntencionVotoAttribute(){ 
        $ejercicio_a_favor = Ejercicio::where('a_favor','A FAVOR')  
        ->join('manzanas', 'ejercicios.manzana_id', '=', 'manzanas.id')
        ->join('secciones', 'manzanas.seccion_id', '=', 'secciones.id')
        ->join('zonas', 'secciones.zona_id', '=', 'zonas.id')
        ->where('zonas.id', $this->id)
        ->get();

        return count($ejercicio_a_favor);
    }
}
