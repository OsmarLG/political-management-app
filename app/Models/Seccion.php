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

    public function asignacionesGeograficas(): MorphMany
    {
        return $this->morphMany(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }

    // Si solo debe haber una asignación por seccion
    public function asignacionGeografica()
    {
        return $this->morphOne(AsignacionGeografica::class, 'asignable', 'modelo', 'id_modelo');
    }
}
