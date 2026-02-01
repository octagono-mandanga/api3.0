<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionGrade extends Model
{
    use HasUuids;

    protected $table = 'core.institution_grades';

    protected $fillable = [
        'institution_id', 'grade_id', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }
}
