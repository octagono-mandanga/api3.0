<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Lectivo extends Model
{
    use HasUuids;

    protected $table = 'core.lectivos';

    protected $fillable = [
        'institucion_id',
        'anio',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'es_actual',
        'estado',
    ];

    protected $casts = [
        'anio' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'es_actual' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function cursos()
    {
        return $this->hasMany(\App\Models\Inscripcion\Curso::class, 'lectivo_id');
    }

    public function docentesAsignatura()
    {
        return $this->hasMany(\App\Models\Inscripcion\DocenteAsignatura::class, 'lectivo_id');
    }

    public function periodos()
    {
        return $this->hasMany(\App\Models\Evaluacion\Periodo::class, 'lectivo_id');
    }

    public function horarios()
    {
        return $this->hasMany(\App\Models\Horario\Horario::class, 'lectivo_id');
    }

    public function lectivosNivel()
    {
        return $this->hasMany(LectivoNivel::class, 'lectivo_id');
    }

    /**
     * Obtiene las fechas del lectivo para un nivel específico.
     * Si existe configuración por nivel, la retorna; si no, retorna las fechas generales.
     */
    public function getFechasParaNivel($nivelId)
    {
        $lectivoNivel = $this->lectivosNivel()->where('nivel_id', $nivelId)->first();

        if ($lectivoNivel) {
            return [
                'fecha_inicio' => $lectivoNivel->fecha_inicio,
                'fecha_fin' => $lectivoNivel->fecha_fin,
            ];
        }

        return [
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
        ];
    }
}
