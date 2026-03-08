<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    protected $table = 'core.temas';

    protected $fillable = [
        'nombre',
        'color_primario',
        'color_secundario',
        'color_terciario',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function instituciones()
    {
        return $this->hasMany(Institucion::class, 'tema_id');
    }
}
