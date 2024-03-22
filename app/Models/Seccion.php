<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seccion extends Model
{
    use HasFactory;
    use SoftDeletes; // Opcional, si deseas utilizar borrado suave.

    protected $table = 'secciones';
    
    protected $fillable = [
        'zona_id',
        'nombre',
        'descripcion',
        'status',
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    public function manzanas()
    {
        return $this->hasMany(Manzana::class, 'seccion_id');
    }

    public function casillas()
    {
        return $this->hasMany(Casilla::class);
    }

    public function asignacionesGeograficas(): MorphMany
    {
        return $this->morphMany(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }

    // Si solo debe haber una asignación por seccion
    public function asignacionGeografica()
    {
        return $this->morphOne(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }

    public function getIntencionVotoAttribute(){ 
        $ejercicio_a_favor = Ejercicio::where('a_favor','A FAVOR')  
        ->join('manzanas', 'ejercicios.manzana_id', '=', 'manzanas.id')
        ->join('secciones', 'manzanas.seccion_id', '=', 'secciones.id')
        ->where('secciones.id', $this->id)
        ->get();

        return count($ejercicio_a_favor);
    }
}
