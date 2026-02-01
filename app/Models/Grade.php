<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasUuids;

    protected $table = 'core.grades';

    protected $fillable = [
        'educational_level_id', 'short_name', 'full_name', 'order'
    ];

    public function educationalLevel(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class, 'educational_level_id');
    }
}
