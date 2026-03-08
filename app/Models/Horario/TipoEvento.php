<?php

namespace App\Models\Horario;

use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model
{
    protected $table = 'horario.tipos_evento';

    protected $fillable = [
        'nombre',
        'color',
        'icono',
        'requiere_asistencia',
        'estado',
    ];

    protected $casts = [
        'requiere_asistencia' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'tipo_id');
    }
}
