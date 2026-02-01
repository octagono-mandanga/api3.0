<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormationArea extends Model
{
    use HasUuids;

    protected $table = 'core.formation_areas';

    protected $fillable = [
        'educational_level_id',
        'name',
        'short_name',
        'status',
        'is_mandatory'
    ];

    protected $casts = [
        'is_mandatory' => 'boolean'
    ];

    /**
     * Relación con el nivel educativo
     */
    public function educationalLevel(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class, 'educational_level_id');
    }
}
