<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Etnia extends Model
{
    protected $table = 'ref.etnias';

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
        return $this->hasMany(\App\Models\Auth\Usuario::class, 'etnia_id');
    }
}
