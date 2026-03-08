<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'core.planes';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'max_usuarios',
        'max_estudiantes',
        'estado',
    ];

    protected $casts = [
        'max_usuarios' => 'integer',
        'max_estudiantes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function instituciones()
    {
        return $this->hasMany(Institucion::class, 'plan_id');
    }
}
