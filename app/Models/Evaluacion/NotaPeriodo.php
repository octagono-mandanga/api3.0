<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NotaPeriodo extends Model
{
    use HasUuids;

    protected $table = 'evaluacion.notas_periodo';

    protected $fillable = [
        'matricula_id',
        'asignatura_id',
        'periodo_id',
        'nota_definitiva',
        'nota_recuperacion',
        'nota_final',
        'observacion',
        'logro_id',
        'aprobado',
    ];

    protected $casts = [
        'nota_definitiva' => 'decimal:2',
        'nota_recuperacion' => 'decimal:2',
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

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function logro()
    {
        return $this->belongsTo(\App\Models\Academico\Logro::class, 'logro_id');
    }
}
