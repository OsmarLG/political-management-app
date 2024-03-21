<?php

namespace App\Models;

use App\Models\AsignacionGeografica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barda extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bardas';
    
    protected $fillable = [
        'id',
        'identificador',
        'seccion_id',
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
