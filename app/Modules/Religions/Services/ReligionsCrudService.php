<?php

declare(strict_types=1);

namespace App\Modules\Religions\Services;

use App\Modules\Religions\DTO\EntityListQueryData;
use Illuminate\Database\Eloquent\Model;

class ReligionsCrudService
{
    public function __construct(
        private readonly ReligionsEntityRegistry $registry,
        private readonly ReligionsListQueryService $listQueryService,
        private readonly ReligionsFormSchemaService $formSchemaService,
        private readonly ReligionsPayloadMapperService $payloadMapperService,
        private readonly ReligionsPersistenceService $persistenceService,
    ) {}

    /**
     * @return array<string, class-string<Model>>
     */
    public function modelMap(): array
    {
        return $this->registry->modelMap();
    }

    /**
     * @return array<int, string>
     */
    public function entities(): array
    {
        return $this->registry->entities();
    }

    /**
     * @return array<string, string>
     */
    public function entityLabels(): array
    {
        return $this->registry->entityLabels();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function list(string $entity, EntityListQueryData $queryData): array
    {
        return $this->listQueryService->list($entity, $queryData);
    }

    /**
     * @return array<string, mixed>
     */
    public function listPaginated(string $entity, EntityListQueryData $queryData): array
    {
        return $this->listQueryService->listPaginated($entity, $queryData);
    }

    /**
     * @return array<int, array{name:string,label:string}>
     */
    public function tableColumns(string $entity): array
    {
        return $this->listQueryService->tableColumns($entity);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $entity, int $id): ?array
    {
        return $this->listQueryService->find($entity, $id);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fieldDefinitions(string $entity): array
    {
        return $this->formSchemaService->fieldDefinitions($entity);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function buildPayloadFromForm(string $entity, array $input): array
    {
        return $this->payloadMapperService->buildPayloadFromForm($entity, $input);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function create(string $entity, array $payload): array
    {
        return $this->persistenceService->create($entity, $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(string $entity, int $id, array $payload): array
    {
        return $this->persistenceService->update($entity, $id, $payload);
    }

    public function delete(string $entity, int $id): void
    {
        $this->persistenceService->delete($entity, $id);
    }
}
