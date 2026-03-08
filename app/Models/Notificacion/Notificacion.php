<?php

namespace App\Models\Notificacion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasUuids;

    protected $table = 'notificacion.notificaciones';

    protected $fillable = [
        'usuario_id',
        'institucion_id',
        'tipo_id',
        'titulo',
        'contenido',
        'data',
        'accion_url',
        'leida',
        'fecha_lectura',
        'enviada_push',
        'enviada_email',
        'fecha_expiracion',
    ];

    protected $casts = [
        'tipo_id' => 'integer',
        'data' => 'array',
        'leida' => 'boolean',
        'fecha_lectura' => 'datetime',
        'enviada_push' => 'boolean',
        'enviada_email' => 'boolean',
        'fecha_expiracion' => 'datetime',
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

    public function tipo()
    {
        return $this->belongsTo(TipoNotificacion::class, 'tipo_id');
    }
}
