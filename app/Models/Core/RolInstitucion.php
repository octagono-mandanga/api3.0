<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class RolInstitucion extends Model
{
    protected $table = 'core.roles_institucion';

    protected $fillable = [
        'institucion_id',
        'rol_id',
        'alias',
        'permisos_extra',
        'estado',
    ];

    protected $casts = [
        'rol_id' => 'integer',
        'permisos_extra' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function rol()
    {
        return $this->belongsTo(\App\Models\Auth\Rol::class, 'rol_id');
    }
}
