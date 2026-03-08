<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Discapacidad extends Model
{
    protected $table = 'ref.discapacidades';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
    ];

    public function usuarios()
    {
        return $this->hasMany(\App\Models\Auth\Usuario::class, 'discapacidad_id');
    }
}
