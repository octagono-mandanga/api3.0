<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Eloquent\Model;

class RangoEscala extends Model
{
    protected $table = 'evaluacion.rangos_escala';

    protected $fillable = [
        'escala_id',
        'desde',
        'hasta',
        'desempeno',
        'abreviatura',
        'color',
    ];

    protected $casts = [
        'desde' => 'decimal:2',
        'hasta' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function escala()
    {
        return $this->belongsTo(EscalaCalificacion::class, 'escala_id');
    }
}
