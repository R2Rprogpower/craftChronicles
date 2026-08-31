<?php

declare(strict_types=1);

namespace App\Modules\ServiceRequests\Presentations;

use App\Core\Abstracts\Presentation;
use App\Models\ServiceRequest;

class ServiceRequestStorePresentation extends Presentation
{
    /** @return array<int|string, mixed> */
    public function present(mixed $data): array
    {
        if (! $data instanceof ServiceRequest) {
            return parent::present($data);
        }

        return ['id' => $data->id, 'status' => $data->status, 'created_at' => $data->created_at?->toIso8601String()];
    }
}
