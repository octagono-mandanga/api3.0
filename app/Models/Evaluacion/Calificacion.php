<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasUuids;

    protected $table = 'evaluacion.calificaciones';

    protected $fillable = [
        'actividad_id',
        'matricula_id',
        'nota',
        'observacion',
        'fecha_calificacion',
        'entregado',
        'fecha_entrega',
        'estado',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
        'fecha_calificacion' => 'datetime',
        'entregado' => 'boolean',
        'fecha_entrega' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function matricula()
    {
        return $this->belongsTo(\App\Models\Inscripcion\Matricula::class, 'matricula_id');
    }
}
