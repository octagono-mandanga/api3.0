<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
    protected $table = 'ref.eps';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
    ];

    public function usuarios()
    {
        return $this->hasMany(\App\Models\Auth\Usuario::class, 'eps_id');
    }
}
