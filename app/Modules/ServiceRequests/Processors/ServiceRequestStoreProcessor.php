<?php

declare(strict_types=1);

namespace App\Modules\ServiceRequests\Processors;

use App\Core\Abstracts\Processor;
use App\Models\ServiceRequest;
use App\Modules\ServiceRequests\DTO\CreateServiceRequestDTO;
use App\Modules\ServiceRequests\Http\Requests\StoreServiceRequestRequest;
use App\Modules\ServiceRequests\Services\ServiceRequestService;

class ServiceRequestStoreProcessor extends Processor
{
    public function __construct(private readonly ServiceRequestService $service) {}

    public function execute(StoreServiceRequestRequest $request): ServiceRequest
    {
        $data = $request->validated();

        return $this->service->create(new CreateServiceRequestDTO(
            name: $data['name'], email: $data['email'] ?? null, contact: $data['contact'] ?? null,
            company: $data['company'] ?? null, serviceOfferingId: $data['service_id'] ?? null,
            budget: $data['budget'] ?? null, message: $data['message'],
        ));
    }
}
