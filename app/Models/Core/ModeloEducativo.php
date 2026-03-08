<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ModeloEducativo extends Model
{
    protected $table = 'core.modelos_educativos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
