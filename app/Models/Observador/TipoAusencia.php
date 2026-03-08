<?php

namespace App\Models\Observador;

use Illuminate\Database\Eloquent\Model;

class TipoAusencia extends Model
{
    protected $table = 'observador.tipos_ausencia';

    protected $fillable = [
        'nombre',
        'codigo',
        'justificable',
        'estado',
    ];

    protected $casts = [
        'justificable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'tipo_ausencia_id');
    }
}
