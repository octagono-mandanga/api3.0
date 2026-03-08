<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Model;

class AsignaturaGrado extends Model
{
    protected $table = 'academico.asignaturas_grado';

    protected $fillable = [
        'asignatura_id',
        'grado_id',
        'intensidad_horaria',
        'estado',
    ];

    protected $casts = [
        'grado_id' => 'integer',
        'intensidad_horaria' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function grado()
    {
        return $this->belongsTo(\App\Models\Core\Grado::class, 'grado_id');
    }
}
