<?php

declare(strict_types=1);

namespace App\Modules\Religions\Http\Controllers;

use App\Core\Responses\SuccessResponse;
use App\Http\Controllers\Controller;
use App\Modules\Religions\DTO\EntityListQueryData;
use App\Modules\Religions\Services\ReligionsCrudService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReligionsCrudController extends Controller
{
    public function __construct(
        private readonly ReligionsCrudService $service
    ) {}

    public function index(Request $request, string $entity): SuccessResponse
    {
        return new SuccessResponse(
            $this->service->list($entity, EntityListQueryData::fromArray($request->query()))
        );
    }

    public function store(Request $request, string $entity): SuccessResponse
    {
        return new SuccessResponse(
            $this->service->create($entity, $request->all()),
            ['message' => 'Created successfully'],
            Response::HTTP_CREATED
        );
    }

    public function update(Request $request, string $entity, int $id): SuccessResponse
    {
        return new SuccessResponse(
            $this->service->update($entity, $id, $request->all()),
            ['message' => 'Updated successfully']
        );
    }

    public function destroy(string $entity, int $id): SuccessResponse
    {
        $this->service->delete($entity, $id);

        return new SuccessResponse(
            ['deleted' => true],
            ['message' => 'Deleted successfully']
        );
    }
}
