<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Model;

class AreaFormacion extends Model
{
    protected $table = 'academico.areas_formacion';

    protected $fillable = [
        'nombre',
        'codigo',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function asignaturas()
    {
        return $this->hasMany(Asignatura::class, 'area_id');
    }

    public function areasInstitucion()
    {
        return $this->hasMany(AreaInstitucion::class, 'area_id');
    }

    public function instituciones()
    {
        return $this->belongsToMany(
            \App\Models\Core\Institucion::class,
            'academico.areas_institucion',
            'area_id',
            'institucion_id'
        )->withPivot('nivel_id', 'estado')->withTimestamps();
    }
}
