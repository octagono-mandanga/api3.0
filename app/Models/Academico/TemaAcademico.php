<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TemaAcademico extends Model
{
    use HasUuids;

    protected $table = 'academico.temas';

    protected $fillable = [
        'unidad_id',
        'nombre',
        'descripcion',
        'orden',
        'estado',
    ];

    protected $casts = [
        'orden' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function unidad()
    {
        return $this->belongsTo(UnidadTematica::class, 'unidad_id');
    }
}
