<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrandProgressArea extends Model
{
    protected $fillable = ['name', 'slug', 'status', 'progress', 'summary', 'goals', 'tasks', 'notes', 'next_steps', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['goals' => 'array', 'tasks' => 'array', 'notes' => 'array', 'next_steps' => 'array', 'active' => 'boolean'];
    }

    /** @return HasMany<ProgressMilestone, $this> */
    public function milestones(): HasMany
    {
        $relation = $this->hasMany(ProgressMilestone::class);
        $relation->orderBy('sort_order');

        return $relation;
    }
}
