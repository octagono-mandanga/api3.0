<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ActiveSession extends Model
{
    use HasUuids;

    protected $table = 'auth.active_sessions';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'institution_id', 'token_id', 'last_activity', 'ip_address', 'is_online'
    ];
}
