<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionUserRole extends Model
{
    protected $table = 'auth.institution_user_roles';
    
    // Indicamos que no es incrementable porque usas UUIDs
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Relación con el modelo Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
