<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Eloquent\Model;

class TipoActividad extends Model
{
    protected $table = 'evaluacion.tipos_actividad';

    protected $fillable = [
        'nombre',
        'codigo',
        'icono',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'tipo_id');
    }
}
