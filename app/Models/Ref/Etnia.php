<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Etnia extends Model
{
    protected $table = 'ref.etnias';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
    ];

    public function usuarios()
    {
        return $this->hasMany(\App\Models\Auth\Usuario::class, 'etnia_id');
    }
}
