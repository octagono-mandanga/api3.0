<?php

namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RegistroAcceso extends Model
{
    use HasUuids;

    protected $table = 'auditoria.registros_acceso';

    protected $fillable = [
        'usuario_id',
        'tipo_evento',
        'exitoso',
        'ip',
        'user_agent',
        'metodo_auth',
        'detalles',
    ];

    protected $casts = [
        'exitoso' => 'boolean',
        'detalles' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }
}
