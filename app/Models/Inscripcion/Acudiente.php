<?php

namespace App\Models\Inscripcion;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Acudiente extends Model
{
    use HasUuids;

    protected $table = 'inscripcion.acudientes';

    protected $fillable = [
        'usuario_id',
        'estudiante_id',
        'parentesco_id',
        'es_principal',
        'autorizado_recoger',
        'estado',
    ];

    protected $casts = [
        'parentesco_id' => 'integer',
        'es_principal' => 'boolean',
        'autorizado_recoger' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'usuario_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function parentesco()
    {
        return $this->belongsTo(TipoParentesco::class, 'parentesco_id');
    }
}
