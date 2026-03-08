<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasUuids;

    protected $table = 'core.perfiles';

    protected $fillable = [
        'usuario_id',
        'institucion_id',
        'sede_id',
        'rol_id',
        'cargo',
        'es_principal',
        'estado',
    ];

    protected $casts = [
        'rol_id' => 'integer',
        'es_principal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function rol()
    {
        return $this->belongsTo(\App\Models\Auth\Rol::class, 'rol_id');
    }
}
