<?php

namespace App\Models\Horario;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasUuids;

    protected $table = 'horario.horarios';

    protected $fillable = [
        'institucion_id',
        'curso_id',
        'docente_asignatura_id',
        'franja_id',
        'dia_semana',
        'aula',
        'estado',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function curso()
    {
        return $this->belongsTo(\App\Models\Inscripcion\Curso::class, 'curso_id');
    }

    public function docenteAsignatura()
    {
        return $this->belongsTo(\App\Models\Inscripcion\DocenteAsignatura::class, 'docente_asignatura_id');
    }

    public function franja()
    {
        return $this->belongsTo(FranjaHoraria::class, 'franja_id');
    }
}
