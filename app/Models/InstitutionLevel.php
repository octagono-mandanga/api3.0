<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionLevel extends Model
{
    use HasUuids;

    protected $table = 'core.institution_educational_levels';

    protected $fillable = [
        'institution_id', 'educational_level_id', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function educationalLevel(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class, 'educational_level_id');
    }
}
