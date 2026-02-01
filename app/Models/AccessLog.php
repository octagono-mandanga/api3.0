<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AccessLog extends Model
{
    use HasUuids;

    protected $table = 'auth.access_logs';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'institution_id', 'event_type', 'ip_address', 
        'user_agent', 'os', 'location_city', 'status_code'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
