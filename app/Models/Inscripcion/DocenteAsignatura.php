<?php

namespace App\Models\Inscripcion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DocenteAsignatura extends Model
{
    use HasUuids;

    protected $table = 'inscripcion.docentes_asignatura';

    protected $fillable = [
        'usuario_id',
        'asignatura_id',
        'curso_id',
        'lectivo_id',
        'es_titular',
        'estado',
    ];

    protected $casts = [
        'es_titular' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(\App\Models\Academico\Asignatura::class, 'asignatura_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function lectivo()
    {
        return $this->belongsTo(\App\Models\Core\Lectivo::class, 'lectivo_id');
    }
}
