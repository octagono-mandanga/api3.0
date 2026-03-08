<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
    protected $table = 'ref.eps';

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
        return $this->hasMany(\App\Models\Auth\Usuario::class, 'eps_id');
    }
}
