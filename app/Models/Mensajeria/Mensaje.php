<?php

namespace App\Models\Mensajeria;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasUuids;

    protected $table = 'mensajeria.mensajes';

    protected $fillable = [
        'conversacion_id',
        'autor_id',
        'contenido',
        'adjuntos',
        'respuesta_a',
        'editado',
        'fecha_edicion',
        'eliminado',
        'fecha_eliminacion',
    ];

    protected $casts = [
        'adjuntos' => 'array',
        'editado' => 'boolean',
        'fecha_edicion' => 'datetime',
        'eliminado' => 'boolean',
        'fecha_eliminacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class, 'conversacion_id');
    }

    public function autor()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'autor_id');
    }

    public function mensajeOriginal()
    {
        return $this->belongsTo(Mensaje::class, 'respuesta_a');
    }

    public function respuestas()
    {
        return $this->hasMany(Mensaje::class, 'respuesta_a');
    }

    public function lecturas()
    {
        return $this->hasMany(Lectura::class, 'mensaje_id');
    }
}
