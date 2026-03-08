<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Model;

class TipoCompetencia extends Model
{
    protected $table = 'academico.tipos_competencia';

    protected $fillable = [
        'nombre',
        'codigo',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function competencias()
    {
        return $this->hasMany(Competencia::class, 'tipo_id');
    }
}
