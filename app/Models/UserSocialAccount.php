<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserSocialAccount extends Model
{
    use HasUuids;

    protected $table = 'auth.user_social_accounts';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'provider', 'provider_user_id', 'provider_email'
    ];

    public $timestamps = false; // Según tu captura, solo tiene linked_at
}
