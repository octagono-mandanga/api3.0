<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'ref.departamentos';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
    ];

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'departamento_id');
    }
}
