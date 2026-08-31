<?php

declare(strict_types=1);

namespace App\Modules\ServiceRequests\Http\Controllers;

use App\Core\Responses\SuccessResponse;
use App\Http\Controllers\Controller;
use App\Modules\ServiceRequests\Http\Requests\StoreServiceRequestRequest;
use App\Modules\ServiceRequests\Presentations\ServiceRequestStorePresentation;
use App\Modules\ServiceRequests\Processors\ServiceRequestStoreProcessor;
use Illuminate\Http\Response;

class ServiceRequestController extends Controller
{
    public function store(StoreServiceRequestRequest $request, ServiceRequestStoreProcessor $processor, ServiceRequestStorePresentation $presentation): SuccessResponse
    {
        return new SuccessResponse($presentation->present($processor->execute($request)), ['message' => 'Service request received'], Response::HTTP_CREATED);
    }
}
