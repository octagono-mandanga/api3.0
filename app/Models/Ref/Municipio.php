<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'ref.municipios';
    public $timestamps = false;

    protected $fillable = [
        'departamento_id',
        'codigo',
        'nombre',
        'estado',
    ];

    protected $casts = [
        'departamento_id' => 'integer',
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
