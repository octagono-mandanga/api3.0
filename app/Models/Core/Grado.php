<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    protected $table = 'core.grados';

    protected $fillable = [
        'nivel_id',
        'nombre',
        'codigo',
        'orden',
        'estado',
    ];

    protected $casts = [
        'nivel_id' => 'integer',
        'orden' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function nivel()
    {
        return $this->belongsTo(NivelEducativo::class, 'nivel_id');
    }

    public function gradosInstitucion()
    {
        return $this->hasMany(GradoInstitucion::class, 'grado_id');
    }

    public function asignaturasGrado()
    {
        return $this->hasMany(\App\Models\Academico\AsignaturaGrado::class, 'grado_id');
    }

    public function logros()
    {
        return $this->hasMany(\App\Models\Academico\Logro::class, 'grado_id');
    }

    public function unidadesTematicas()
    {
        return $this->hasMany(\App\Models\Academico\UnidadTematica::class, 'grado_id');
    }

    public function cursos()
    {
        return $this->hasMany(\App\Models\Inscripcion\Curso::class, 'grado_id');
    }
}
