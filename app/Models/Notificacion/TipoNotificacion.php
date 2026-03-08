<?php

namespace App\Models\Notificacion;

use Illuminate\Database\Eloquent\Model;

class TipoNotificacion extends Model
{
    protected $table = 'notificacion.tipos_notificacion';

    protected $fillable = [
        'nombre',
        'codigo',
        'categoria',
        'plantilla_titulo',
        'plantilla_cuerpo',
        'icono',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'tipo_id');
    }

    public function preferencias()
    {
        return $this->hasMany(Preferencia::class, 'tipo_notificacion_id');
    }
}
