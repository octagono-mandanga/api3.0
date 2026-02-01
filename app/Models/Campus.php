<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campus extends Model
{
    use HasUuids;

    protected $table = 'core.campuses'; // Esquema core

    protected $fillable = [
        'institution_id', 'name', 'is_main', 'status', 
        'address', 'phone', 'city_id', 'location'
    ];

    /**
     * Relación con la institución en el esquema auth
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }
}
