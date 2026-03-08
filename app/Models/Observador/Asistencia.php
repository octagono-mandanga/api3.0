<?php

namespace App\Models\Observador;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasUuids;

    protected $table = 'observador.asistencias';

    protected $fillable = [
        'institucion_id',
        'matricula_id',
        'curso_id',
        'asignatura_id',
        'fecha',
        'presente',
        'tipo_ausencia_id',
        'justificada',
        'justificacion',
        'registrado_por',
    ];

    protected $casts = [
        'tipo_ausencia_id' => 'integer',
        'fecha' => 'date',
        'presente' => 'boolean',
        'justificada' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function matricula()
    {
        return $this->belongsTo(\App\Models\Inscripcion\Matricula::class, 'matricula_id');
    }

    public function curso()
    {
        return $this->belongsTo(\App\Models\Inscripcion\Curso::class, 'curso_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(\App\Models\Academico\Asignatura::class, 'asignatura_id');
    }

    public function tipoAusencia()
    {
        return $this->belongsTo(TipoAusencia::class, 'tipo_ausencia_id');
    }

    public function registrador()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'registrado_por');
    }
}
