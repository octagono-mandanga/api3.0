<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Discapacidad extends Model
{
    protected $table = 'ref.discapacidades';

    protected $fillable = [
        'nombre',
        'codigo',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuarios()
    {
        return $this->hasMany(\App\Models\Auth\Usuario::class, 'discapacidad_id');
    }
}
