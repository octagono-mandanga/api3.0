<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Logro extends Model
{
    use HasUuids;

    protected $table = 'academico.logros';

    protected $fillable = [
        'institucion_id',
        'competencia_id',
        'asignatura_id',
        'grado_id',
        'descripcion',
        'codigo',
        'estado',
    ];

    protected $casts = [
        'grado_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function competencia()
    {
        return $this->belongsTo(Competencia::class, 'competencia_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function grado()
    {
        return $this->belongsTo(\App\Models\Core\Grado::class, 'grado_id');
    }

    public function actividades()
    {
        return $this->hasMany(\App\Models\Evaluacion\Actividad::class, 'logro_id');
    }

    public function notasPeriodo()
    {
        return $this->hasMany(\App\Models\Evaluacion\NotaPeriodo::class, 'logro_id');
    }
}
