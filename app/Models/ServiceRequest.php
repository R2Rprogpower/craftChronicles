<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 */
class ServiceRequest extends Model
{
    protected $fillable = ['name', 'email', 'contact', 'company', 'service_offering_id', 'budget', 'message', 'status', 'source', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    /** @return BelongsTo<ServiceOffering, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(ServiceOffering::class, 'service_offering_id');
    }
}
