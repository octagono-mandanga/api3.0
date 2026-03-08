<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'ref.tipos_documento';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
    ];

    public function usuarios()
    {
        return $this->hasMany(\App\Models\Auth\Usuario::class, 'tipo_documento_id');
    }
}
