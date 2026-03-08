<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NotaFinal extends Model
{
    use HasUuids;

    protected $table = 'evaluacion.notas_finales';

    protected $fillable = [
        'matricula_id',
        'asignatura_id',
        'nota_definitiva',
        'nota_habilitacion',
        'nota_final',
        'observacion',
        'aprobado',
    ];

    protected $casts = [
        'nota_definitiva' => 'decimal:2',
        'nota_habilitacion' => 'decimal:2',
        'nota_final' => 'decimal:2',
        'aprobado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function matricula()
    {
        return $this->belongsTo(\App\Models\Inscripcion\Matricula::class, 'matricula_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(\App\Models\Academico\Asignatura::class, 'asignatura_id');
    }
}
