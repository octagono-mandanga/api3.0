<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasUuids;

    protected $table = 'evaluacion.periodos';

    protected $fillable = [
        'institucion_id',
        'lectivo_id',
        'numero',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'peso',
        'es_activo',
        'estado',
    ];

    protected $casts = [
        'numero' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'peso' => 'decimal:2',
        'es_activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function lectivo()
    {
        return $this->belongsTo(\App\Models\Core\Lectivo::class, 'lectivo_id');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'periodo_id');
    }

    public function notasPeriodo()
    {
        return $this->hasMany(NotaPeriodo::class, 'periodo_id');
    }
}
