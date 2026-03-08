<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'ref.departamentos';

    protected $fillable = [
        'nombre',
        'codigo',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'departamento_id');
    }
}
