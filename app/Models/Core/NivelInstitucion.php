<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NivelInstitucion extends Model
{
    use HasUuids;

    protected $table = 'core.niveles_institucion';

    protected $fillable = [
        'institucion_id',
        'nivel_id',
        'estado',
    ];

    protected $casts = [
        'nivel_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function nivel()
    {
        return $this->belongsTo(NivelEducativo::class, 'nivel_id');
    }
}
