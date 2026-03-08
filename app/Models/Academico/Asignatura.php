<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    use HasUuids;

    protected $table = 'academico.asignaturas';

    protected $fillable = [
        'institucion_id',
        'area_id',
        'nombre',
        'codigo',
        'descripcion',
        'horas_semanales',
        'es_obligatoria',
        'estado',
    ];

    protected $casts = [
        'area_id' => 'integer',
        'horas_semanales' => 'integer',
        'es_obligatoria' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function area()
    {
        return $this->belongsTo(AreaFormacion::class, 'area_id');
    }

    public function asignaturasGrado()
    {
        return $this->hasMany(AsignaturaGrado::class, 'asignatura_id');
    }

    public function competencias()
    {
        return $this->hasMany(Competencia::class, 'asignatura_id');
    }

    public function logros()
    {
        return $this->hasMany(Logro::class, 'asignatura_id');
    }

    public function unidadesTematicas()
    {
        return $this->hasMany(UnidadTematica::class, 'asignatura_id');
    }

    public function docentesAsignatura()
    {
        return $this->hasMany(\App\Models\Inscripcion\DocenteAsignatura::class, 'asignatura_id');
    }

    public function actividades()
    {
        return $this->hasMany(\App\Models\Evaluacion\Actividad::class, 'asignatura_id');
    }

    public function notasPeriodo()
    {
        return $this->hasMany(\App\Models\Evaluacion\NotaPeriodo::class, 'asignatura_id');
    }

    public function notasFinales()
    {
        return $this->hasMany(\App\Models\Evaluacion\NotaFinal::class, 'asignatura_id');
    }

    public function asistencias()
    {
        return $this->hasMany(\App\Models\Observador\Asistencia::class, 'asignatura_id');
    }

    public function horarios()
    {
        return $this->hasMany(\App\Models\Horario\Horario::class, 'asignatura_id');
    }
}
