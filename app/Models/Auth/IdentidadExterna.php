<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class IdentidadExterna extends Model
{
    use HasUuids;

    protected $table = 'auth.identidades_externas';

    protected $fillable = [
        'usuario_id',
        'proveedor',
        'proveedor_id',
        'email',
        'nombre',
        'avatar_url',
        'datos_extra',
    ];

    protected $casts = [
        'datos_extra' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
