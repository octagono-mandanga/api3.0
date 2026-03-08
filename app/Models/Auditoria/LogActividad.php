<?php

namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LogActividad extends Model
{
    use HasUuids;

    protected $table = 'auditoria.logs_actividad';

    protected $fillable = [
        'usuario_id',
        'institucion_id',
        'accion',
        'entidad',
        'entidad_id',
        'valores_anteriores',
        'valores_nuevos',
        'ip',
        'user_agent',
        'descripcion',
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }
}
