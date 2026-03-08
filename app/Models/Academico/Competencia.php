<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    use HasUuids;

    protected $table = 'academico.competencias';

    protected $fillable = [
        'institucion_id',
        'tipo_id',
        'asignatura_id',
        'nombre',
        'codigo',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'tipo_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoCompetencia::class, 'tipo_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function logros()
    {
        return $this->hasMany(Logro::class, 'competencia_id');
    }
}
