<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasUuids;

    protected $table = 'core.sedes';

    protected $fillable = [
        'institucion_id',
        'municipio_id',
        'nombre',
        'codigo',
        'es_principal',
        'direccion',
        'telefono',
        'latitud',
        'longitud',
        'estado',
    ];

    protected $casts = [
        'municipio_id' => 'integer',
        'es_principal' => 'boolean',
        'latitud'      => 'decimal:8',
        'longitud'     => 'decimal:8',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function municipio()
    {
        return $this->belongsTo(\App\Models\Ref\Municipio::class, 'municipio_id');
    }

    public function perfiles()
    {
        return $this->hasMany(Perfil::class, 'sede_id');
    }

    public function cursos()
    {
        return $this->hasMany(\App\Models\Inscripcion\Curso::class, 'sede_id');
    }

    public function eventos()
    {
        return $this->hasMany(\App\Models\Horario\Evento::class, 'sede_id');
    }
}
