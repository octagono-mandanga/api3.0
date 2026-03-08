<?php

namespace App\Models\Observador;

use Illuminate\Database\Eloquent\Model;

class TipoObservacion extends Model
{
    protected $table = 'observador.tipos_observacion';

    protected $fillable = [
        'nombre',
        'valoracion',
        'color',
        'icono',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function observaciones()
    {
        return $this->hasMany(Observacion::class, 'tipo_id');
    }
}
