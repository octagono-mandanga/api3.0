<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasUuids;

    protected $table = 'auth.roles';

    // Desactivamos el incremento numérico y definimos el tipo como string
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['name', 'slug', 'is_active', 'branding_colors'];

    protected $casts = [
        'branding_colors' => 'array',
        'is_active' => 'boolean'
    ];
}
