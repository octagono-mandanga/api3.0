<?php

namespace App\Models\Mensajeria;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    use HasUuids;

    protected $table = 'mensajeria.participantes';

    protected $fillable = [
        'conversacion_id',
        'usuario_id',
        'rol',
        'silenciado',
        'fecha_union',
        'fecha_salida',
    ];

    protected $casts = [
        'silenciado' => 'boolean',
        'fecha_union' => 'datetime',
        'fecha_salida' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class, 'conversacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }

    public function lecturas()
    {
        return $this->hasMany(Lectura::class, 'participante_id');
    }
}
