<?php

namespace App\Models\Horario;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FranjaHoraria extends Model
{
    use HasUuids;

    protected $table = 'horario.franjas_horarias';

    protected $fillable = [
        'institucion_id',
        'sede_id',
        'jornada_id',
        'nombre',
        'hora_inicio',
        'hora_fin',
        'tipo',
        'orden',
        'estado',
    ];

    protected $casts = [
        'jornada_id' => 'integer',
        'hora_inicio' => 'datetime:H:i:s',
        'hora_fin' => 'datetime:H:i:s',
        'orden' => 'integer',
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

    public function jornada()
    {
        return $this->belongsTo(\App\Models\Core\Jornada::class, 'jornada_id');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'franja_id');
    }
}
