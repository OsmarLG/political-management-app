<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioAsignacion extends Model
{
    use HasFactory;

    protected $table = 'usuario_asignaciones';
    
    protected $fillable = [
        'user_id',
        'modelo',
        'id_modelo',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asignable()
    {
        return $this->morphTo(null, 'modelo', 'id_modelo');
    }
}
