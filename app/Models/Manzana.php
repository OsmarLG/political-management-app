<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manzana extends Model
{
    use HasFactory;
    use SoftDeletes; // Opcional, si deseas utilizar borrado suave.

    protected $table = 'manzanas';
    
    protected $fillable = [
        'seccion_id',
        'nombre',
        'descripcion',
        'status',
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function asignacionesGeograficas(): MorphMany
    {
        return $this->morphMany(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }

    // Si solo debe haber una asignación por zona
    public function asignacionGeografica()
    {
        return $this->morphOne(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }
}
