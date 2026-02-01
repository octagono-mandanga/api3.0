<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Institution extends Model
{
    use HasUuids;

    protected $table = 'auth.institutions'; 

    protected $fillable = [
        'nit', 'legal_name', 'short_name', 'dane_code', 
        'institution_type', 'official_email', 'website_url', 
        'logo_url', 'status', 'rector_id', 'branding_colors'
    ];

    protected $casts = [
        'branding_colors' => 'array',
    ];

    public function campuses()
    {
        return $this->hasMany(Campus::class, 'institution_id');
    }

    public function rector()
    {
        return $this->belongsTo(User::class, 'rector_id');
    }
}
