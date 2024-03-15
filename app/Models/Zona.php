<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zona extends Model
{
    use HasFactory;
    use SoftDeletes; // Opcional, si deseas utilizar borrado suave.

    protected $table = 'zonas';
    
    protected $fillable = [
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
}
