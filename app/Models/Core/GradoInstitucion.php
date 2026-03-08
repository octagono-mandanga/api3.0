<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class GradoInstitucion extends Model
{
    protected $table = 'core.grados_institucion';

    protected $fillable = [
        'institucion_id',
        'grado_id',
        'alias',
        'estado',
    ];

    protected $casts = [
        'grado_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }
}
