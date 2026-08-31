<?php

declare(strict_types=1);

namespace App\Modules\ServiceRequests\Repositories;

use App\Models\ServiceRequest;
use App\Modules\ServiceRequests\DTO\CreateServiceRequestDTO;

class ServiceRequestRepository
{
    public function create(CreateServiceRequestDTO $dto): ServiceRequest
    {
        /** @var ServiceRequest */
        return ServiceRequest::query()->create($dto->toArray());
    }
}
