<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationalLevel extends Model
{
    use HasUuids;

    protected $table = 'core.educational_levels';

    protected $fillable = [
        'name', 'description', 'order'
    ];

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'educational_level_id');
    }
}
