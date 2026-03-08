<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'ref.municipios';

    protected $fillable = [
        'departamento_id',
        'nombre',
        'codigo',
        'estado',
    ];

    protected $casts = [
        'departamento_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function instituciones()
    {
        return $this->hasMany(\App\Models\Core\Institucion::class, 'municipio_id');
    }

    public function sedes()
    {
        return $this->hasMany(\App\Models\Core\Sede::class, 'municipio_id');
    }

    public function usuarios()
    {
        return $this->hasMany(\App\Models\Auth\Usuario::class, 'municipio_id');
    }
}
