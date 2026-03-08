<?php

namespace App\Models\Inscripcion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasUuids;

    protected $table = 'inscripcion.estudiantes';

    protected $fillable = [
        'usuario_id',
        'institucion_id',
        'codigo_estudiante',
        'fecha_ingreso',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Core\Institucion::class, 'institucion_id');
    }

    public function acudientes()
    {
        return $this->hasMany(Acudiente::class, 'estudiante_id');
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'estudiante_id');
    }
}
