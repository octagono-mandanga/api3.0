<?php

namespace App\Models\Notificacion;

use Illuminate\Database\Eloquent\Model;

class Preferencia extends Model
{
    protected $table = 'notificacion.preferencias';

    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'usuario_id',
        'tipo_notificacion_id',
        'canal_push',
        'canal_email',
        'canal_sms',
    ];

    protected $casts = [
        'tipo_notificacion_id' => 'integer',
        'canal_push' => 'boolean',
        'canal_email' => 'boolean',
        'canal_sms' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }

    public function tipoNotificacion()
    {
        return $this->belongsTo(TipoNotificacion::class, 'tipo_notificacion_id');
    }
}
