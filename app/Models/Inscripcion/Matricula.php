<?php

namespace App\Models\Inscripcion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasUuids;

    protected $table = 'inscripcion.matriculas';

    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'codigo_matricula',
        'fecha_matricula',
        'tipo',
        'repitente',
        'estado',
    ];

    protected $casts = [
        'fecha_matricula' => 'date',
        'repitente' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function calificaciones()
    {
        return $this->hasMany(\App\Models\Evaluacion\Calificacion::class, 'matricula_id');
    }

    public function notasPeriodo()
    {
        return $this->hasMany(\App\Models\Evaluacion\NotaPeriodo::class, 'matricula_id');
    }

    public function notasFinales()
    {
        return $this->hasMany(\App\Models\Evaluacion\NotaFinal::class, 'matricula_id');
    }

    public function observaciones()
    {
        return $this->hasMany(\App\Models\Observador\Observacion::class, 'matricula_id');
    }

    public function asistencias()
    {
        return $this->hasMany(\App\Models\Observador\Asistencia::class, 'matricula_id');
    }
}
