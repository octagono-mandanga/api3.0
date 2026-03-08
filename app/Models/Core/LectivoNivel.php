<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LectivoNivel extends Model
{
    use HasUuids;

    protected $table = 'core.lectivos_nivel';

    protected $fillable = [
        'lectivo_id',
        'nivel_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'nivel_id' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lectivo()
    {
        return $this->belongsTo(Lectivo::class, 'lectivo_id');
    }

    public function nivel()
    {
        return $this->belongsTo(NivelEducativo::class, 'nivel_id');
    }
}
