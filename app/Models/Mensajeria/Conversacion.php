<?php

namespace App\Models\Mensajeria;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
{
    use HasUuids;

    protected $table = 'mensajeria.conversaciones';

    protected $fillable = [
        'institucion_id',
        'asunto',
        'tipo',
        'contexto_tipo',
        'contexto_id',
        'creador_id',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'creador_id');
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class, 'conversacion_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'conversacion_id');
    }
}
