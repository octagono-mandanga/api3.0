<?php

namespace App\Models\Notificacion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    use HasUuids;

    protected $table = 'notificacion.dispositivos';

    protected $fillable = [
        'usuario_id',
        'token',
        'plataforma',
        'nombre_dispositivo',
        'modelo',
        'version_app',
        'ultimo_uso',
        'estado',
    ];

    protected $casts = [
        'ultimo_uso' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }
}
