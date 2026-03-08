<?php

namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SesionActiva extends Model
{
    use HasUuids;

    protected $table = 'auditoria.sesiones_activas';

    protected $fillable = [
        'usuario_id',
        'token_id',
        'ip',
        'user_agent',
        'dispositivo',
        'ubicacion',
        'ultimo_acceso',
        'expira_en',
    ];

    protected $casts = [
        'ultimo_acceso' => 'datetime',
        'expira_en' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }
}
