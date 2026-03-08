<?php

namespace App\Models\Inscripcion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasUuids;

    protected $table = 'inscripcion.cursos';

    protected $fillable = [
        'institucion_id',
        'sede_id',
        'lectivo_id',
        'grado_id',
        'jornada_id',
        'nombre',
        'codigo',
        'director_id',
        'capacidad',
        'aula',
        'estado',
    ];

    protected $casts = [
        'grado_id' => 'integer',
        'jornada_id' => 'integer',
        'capacidad' => 'integer',
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

    public function lectivo()
    {
        return $this->belongsTo(\App\Models\Core\Lectivo::class, 'lectivo_id');
    }

    public function grado()
    {
        return $this->belongsTo(\App\Models\Core\Grado::class, 'grado_id');
    }

    public function jornada()
    {
        return $this->belongsTo(\App\Models\Core\Jornada::class, 'jornada_id');
    }

    public function director()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'director_id');
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'curso_id');
    }

    public function docentesAsignatura()
    {
        return $this->hasMany(DocenteAsignatura::class, 'curso_id');
    }

    public function actividades()
    {
        return $this->hasMany(\App\Models\Evaluacion\Actividad::class, 'curso_id');
    }

    public function asistencias()
    {
        return $this->hasMany(\App\Models\Observador\Asistencia::class, 'curso_id');
    }

    public function horarios()
    {
        return $this->hasMany(\App\Models\Horario\Horario::class, 'curso_id');
    }
}
