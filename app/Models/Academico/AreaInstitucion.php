<?php

namespace App\Models\Academico;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Institucion;
use App\Models\Core\NivelEducativo;

class AreaInstitucion extends Model
{
    use HasUuids;

    protected $table = 'academico.areas_institucion';

    protected $fillable = [
        'institucion_id',
        'area_id',
        'nivel_id',
        'estado',
    ];

    protected $casts = [
        'area_id' => 'integer',
        'nivel_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function area()
    {
        return $this->belongsTo(AreaFormacion::class, 'area_id');
    }

    public function nivel()
    {
        return $this->belongsTo(NivelEducativo::class, 'nivel_id');
    }
}
