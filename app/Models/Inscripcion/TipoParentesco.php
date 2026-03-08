<?php

namespace App\Models\Inscripcion;

use Illuminate\Database\Eloquent\Model;

class TipoParentesco extends Model
{
    protected $table = 'inscripcion.tipos_parentesco';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function acudientes()
    {
        return $this->hasMany(Acudiente::class, 'parentesco_id');
    }
}
