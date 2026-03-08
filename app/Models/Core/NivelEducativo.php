<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class NivelEducativo extends Model
{
    protected $table = 'core.niveles_educativos';

    protected $fillable = [
        'nombre',
        'codigo',
        'orden',
        'estado',
    ];

    protected $casts = [
        'orden' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function grados()
    {
        return $this->hasMany(Grado::class, 'nivel_id');
    }

    public function nivelesInstitucion()
    {
        return $this->hasMany(NivelInstitucion::class, 'nivel_id');
    }

    public function lectivosNivel()
    {
        return $this->hasMany(LectivoNivel::class, 'nivel_id');
    }

    public function areasInstitucion()
    {
        return $this->hasMany(\App\Models\Academico\AreaInstitucion::class, 'nivel_id');
    }
}
