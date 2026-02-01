<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable
{
    use HasApiTokens, HasUuids; // Añade HasUuids

    protected $table = 'auth.users';
    
    // Indica que la llave primaria no es un entero incrementable
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'first_name', 'middle_name', 'last_name_1', 'last_name_2', 
        'email', 'password_hash', 'global_status', 'avatar_url'
    ];

    public function getAvatarUrlAttribute($value)
    {
         return $value ?: asset('default-avatar.png');
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function institutionRoles()
    {
        return $this->hasMany(InstitutionUserRole::class, 'user_id');
    }
    /**
     * Relación con las cuentas sociales (auth.user_social_accounts)
     */
    public function socialAccounts(): HasMany
    {
        // El segundo parámetro es la llave foránea en la tabla social_accounts
        return $this->hasMany(UserSocialAccount::class, 'user_id');
    }
}
