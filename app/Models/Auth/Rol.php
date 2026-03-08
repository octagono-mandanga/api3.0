<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'auth.roles';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'permisos',
        'es_sistema',
        'estado',
    ];

    protected $casts = [
        'permisos' => 'array',
        'es_sistema' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function rolesInstitucion()
    {
        return $this->hasMany(\App\Models\Core\RolInstitucion::class, 'rol_id');
    }

    public function perfiles()
    {
        return $this->hasMany(\App\Models\Core\Perfil::class, 'rol_id');
    }
}
