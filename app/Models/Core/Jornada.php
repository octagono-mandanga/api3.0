<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{
    protected $table = 'core.jornadas';

    protected $fillable = [
        'nombre',
        'hora_inicio',
        'hora_fin',
        'estado',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cursos()
    {
        return $this->hasMany(\App\Models\Inscripcion\Curso::class, 'jornada_id');
    }

    public function franjasHorarias()
    {
        return $this->hasMany(\App\Models\Horario\FranjaHoraria::class, 'jornada_id');
    }
}
