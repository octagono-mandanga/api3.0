<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UnidadTematica extends Model
{
    use HasUuids;

    protected $table = 'academico.unidades_tematicas';

    protected $fillable = [
        'asignatura_id',
        'grado_id',
        'nombre',
        'descripcion',
        'orden',
        'estado',
    ];

    protected $casts = [
        'grado_id' => 'integer',
        'orden' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function grado()
    {
        return $this->belongsTo(\App\Models\Core\Grado::class, 'grado_id');
    }

    public function temas()
    {
        return $this->hasMany(TemaAcademico::class, 'unidad_id');
    }
}
