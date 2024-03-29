<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'apellido_paterno',
        'apellido_materno',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->apellido_paterno} {$this->apellido_materno}";
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVO');
    }

    public function ejercicios(){
        return $this->hasMany(Ejercicio::class);
    }

    public function Asignacion()
    {
        return $this->hasOne(UsuarioAsignacion::class);
    }

    public function getAsignacionTypeAttribute()
    {
        $roles = $this->roles->pluck('name');
        if ($roles->contains('C DISTRITAL')) {
            return 'Zona';
        }
        if ($roles->contains('C ENLACE DE MANZANA')) {
            return 'Seccion';
        }
        if ($roles->contains('MANZANAL')) {
            return 'Manzana';
        }
        return null; // o cualquier valor predeterminado que desees
    }

    public function iniciales(){
        $iniciles = substr($this->name, 0, 1) . substr($this->apellido_paterno, 0, 1) . substr($this->apellido_materno, 0, 1);
        return strtoupper($iniciles);
    }
}
