<?php

declare(strict_types=1);

namespace App\Modules\ServiceRequests\Services;

use App\Models\ServiceRequest;
use App\Modules\ServiceRequests\DTO\CreateServiceRequestDTO;
use App\Modules\ServiceRequests\Repositories\ServiceRequestRepository;

class ServiceRequestService
{
    public function __construct(private readonly ServiceRequestRepository $repository) {}

    public function create(CreateServiceRequestDTO $dto): ServiceRequest
    {
        return $this->repository->create($dto);
    }
}
