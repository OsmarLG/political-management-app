<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionGeografica extends Model
{
    use HasFactory;

    protected $table = 'asignaciones_geograficas';
    
    protected $fillable = [
        'modelo',
        'id_modelo',
        'latitud',
        'longitud',
        'descripcion',
        'status',
    ];

    // Método para obtener el modelo asociado dinámicamente.
    public function asignable()
    {
        return $this->morphTo(null, 'modelo', 'id_modelo');
    }
}
