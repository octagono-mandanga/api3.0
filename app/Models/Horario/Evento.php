<?php

namespace App\Models\Horario;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasUuids;

    protected $table = 'horario.eventos';

    protected $fillable = [
        'institucion_id',
        'sede_id',
        'tipo_id',
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'todo_el_dia',
        'ubicacion',
        'publico_objetivo',
        'curso_id',
        'creador_id',
        'estado',
    ];

    protected $casts = [
        'tipo_id' => 'integer',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'todo_el_dia' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function sede()
    {
        return $this->belongsTo(\App\Models\Core\Sede::class, 'sede_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_id');
    }

    public function curso()
    {
        return $this->belongsTo(\App\Models\Inscripcion\Curso::class, 'curso_id');
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'creador_id');
    }
}
