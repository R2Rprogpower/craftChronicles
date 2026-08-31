<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressMilestone extends Model
{
    protected $fillable = ['brand_progress_area_id', 'title', 'description', 'status', 'target_date', 'completed_at', 'sort_order'];

    protected function casts(): array
    {
        return ['target_date' => 'date', 'completed_at' => 'datetime'];
    }

    /** @return BelongsTo<BrandProgressArea, $this> */
    public function area(): BelongsTo
    {
        return $this->belongsTo(BrandProgressArea::class, 'brand_progress_area_id');
    }
}
