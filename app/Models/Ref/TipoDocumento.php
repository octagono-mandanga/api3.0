<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'ref.tipos_documento';

    protected $fillable = [
        'nombre',
        'codigo',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuarios()
    {
        return $this->hasMany(\App\Models\Auth\Usuario::class, 'tipo_documento_id');
    }
}
