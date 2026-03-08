<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasUuids;

    protected $table = 'evaluacion.actividades';

    protected $fillable = [
        'institucion_id',
        'asignatura_id',
        'curso_id',
        'periodo_id',
        'docente_id',
        'tipo_id',
        'logro_id',
        'titulo',
        'descripcion',
        'fecha_asignacion',
        'fecha_entrega',
        'peso',
        'nota_maxima',
        'permite_entrega_tardia',
        'visible_estudiantes',
        'estado',
    ];

    protected $casts = [
        'tipo_id' => 'integer',
        'fecha_asignacion' => 'date',
        'fecha_entrega' => 'date',
        'peso' => 'decimal:2',
        'nota_maxima' => 'decimal:2',
        'permite_entrega_tardia' => 'boolean',
        'visible_estudiantes' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(\App\Models\Academico\Asignatura::class, 'asignatura_id');
    }

    public function curso()
    {
        return $this->belongsTo(\App\Models\Inscripcion\Curso::class, 'curso_id');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function docente()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'docente_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoActividad::class, 'tipo_id');
    }

    public function logro()
    {
        return $this->belongsTo(\App\Models\Academico\Logro::class, 'logro_id');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'actividad_id');
    }
}
