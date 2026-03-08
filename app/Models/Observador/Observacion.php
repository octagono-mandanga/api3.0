<?php

namespace App\Models\Observador;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    use HasUuids;

    protected $table = 'observador.observaciones';

    protected $fillable = [
        'institucion_id',
        'matricula_id',
        'autor_id',
        'tipo_id',
        'descripcion',
        'fecha',
        'compromiso',
        'seguimiento',
        'notificar_acudiente',
        'visto_por_acudiente',
        'fecha_visto_acudiente',
        'estado',
    ];

    protected $casts = [
        'tipo_id' => 'integer',
        'fecha' => 'date',
        'notificar_acudiente' => 'boolean',
        'visto_por_acudiente' => 'boolean',
        'fecha_visto_acudiente' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function matricula()
    {
        return $this->belongsTo(\App\Models\Inscripcion\Matricula::class, 'matricula_id');
    }

    public function autor()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'autor_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoObservacion::class, 'tipo_id');
    }
}
