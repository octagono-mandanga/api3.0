<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EscalaCalificacion extends Model
{
    use HasUuids;

    protected $table = 'evaluacion.escalas_calificacion';

    protected $fillable = [
        'institucion_id',
        'nombre',
        'nota_minima',
        'nota_maxima',
        'nota_aprobatoria',
        'usa_decimales',
        'decimales',
        'es_default',
        'estado',
    ];

    protected $casts = [
        'nota_minima' => 'decimal:2',
        'nota_maxima' => 'decimal:2',
        'nota_aprobatoria' => 'decimal:2',
        'usa_decimales' => 'boolean',
        'decimales' => 'integer',
        'es_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function rangos()
    {
        return $this->hasMany(RangoEscala::class, 'escala_id');
    }
}
