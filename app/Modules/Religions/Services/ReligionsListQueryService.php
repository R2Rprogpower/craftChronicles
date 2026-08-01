<?php

declare(strict_types=1);

namespace App\Modules\Religions\Services;

use App\Modules\Religions\DTO\EntityListQueryData;
use App\Modules\Religions\DTO\PaginatedEntityListData;
use App\Modules\Religions\DTO\TableColumnData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ReligionsListQueryService
{
    public function __construct(
        private readonly ReligionsEntityRegistry $registry,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function list(string $entity, EntityListQueryData $queryData): array
    {
        $model = $this->registry->resolveModel($entity);

        return $model::query()
            ->latest('id')
            ->limit($queryData->limit)
            ->get()
            ->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function listPaginated(string $entity, EntityListQueryData $queryData): array
    {
        $model = $this->registry->resolveModel($entity);

        $query = $model::query();

        $q = $queryData->q;
        if ($q !== '') {
            $searchColumns = $this->searchableColumns($entity);
            if ($searchColumns !== []) {
                $query->where(function (Builder $builder) use ($searchColumns, $q): void {
                    foreach ($searchColumns as $index => $column) {
                        if ($index === 0) {
                            $builder->where($column, 'like', "%{$q}%");
                        } else {
                            $builder->orWhere($column, 'like', "%{$q}%");
                        }
                    }
                });
            } elseif (is_numeric($q)) {
                $query->whereKey((int) $q);
            }
        }

        $status = $queryData->status;
        if (in_array($status, ['active', 'inactive'], true) && $this->supportsActiveFilter($entity)) {
            $query->where('is_active', $status === 'active');
        }

        $sortable = $this->sortableColumns($entity);
        $sort = $queryData->sort;
        if (! in_array($sort, $sortable, true)) {
            $sort = 'id';
        }

        $dir = $queryData->dir;
        if (! in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'desc';
        }

        $query->orderBy($sort, $dir);
        if ($sort !== 'id') {
            $query->orderByDesc('id');
        }

        $paginator = $query
            ->paginate($queryData->perPage, ['*'], 'page', $queryData->page)
            ->appends([
                'q' => $queryData->q,
                'status' => $queryData->status,
                'per_page' => $queryData->perPage,
                'sort' => $queryData->sort,
                'dir' => $queryData->dir,
            ]);

        $items = $paginator->getCollection()
            ->map(static fn (Model $row): array => $row->toArray())
            ->values()
            ->all();

        $result = new PaginatedEntityListData(
            items: $items,
            meta: [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            filters: [
                'q' => $queryData->q,
                'status' => $queryData->status,
                'per_page' => $queryData->perPage,
                'sort' => $sort,
                'dir' => $dir,
            ],
        );

        return $result->toArray();
    }

    /**
     * @return array<int, array{name:string,label:string,sortable:bool}>
     */
    public function tableColumns(string $entity): array
    {
        $sortable = $this->sortableColumns($entity);

        return match ($entity) {
            'religions' => $this->columns(['id', 'name', 'slug', 'is_active', 'updated_at'], $sortable),
            'confessions' => $this->columns(['id', 'religion_id', 'name', 'slug', 'is_active', 'updated_at'], $sortable),
            'rituals' => $this->columns(['id', 'confession_id', 'ritual_key', 'name', 'is_active', 'updated_at'], $sortable),
            'reminder_types' => $this->columns(['id', 'type_key', 'name', 'is_builtin', 'updated_at'], $sortable),
            'reminder_implementations' => $this->columns(['id', 'reminder_type_id', 'implementation_key', 'implementation_mode', 'is_active', 'updated_at'], $sortable),
            'religion_commands' => $this->columns(['id', 'confession_id', 'command_key', 'trigger', 'is_active', 'updated_at'], $sortable),
            'religion_reminders' => $this->columns(['id', 'confession_id', 'title', 'text_format', 'frequency_mode', 'schedule_timezone', 'is_active', 'updated_at'], $sortable),
            'language_packs' => $this->columns(['id', 'code', 'name', 'script', 'is_active', 'updated_at'], $sortable),
            'language_words' => $this->columns(['id', 'language_pack_id', 'word', 'translation', 'transcription', 'is_active', 'updated_at'], $sortable),
            'user_group_religion_preferences' => $this->columns(['id', 'messenger_user_id', 'messenger_group_link_id', 'confession_id', 'ritual_opt_in', 'is_active', 'updated_at'], $sortable),
            'reminder_delivery_targets' => $this->columns(['id', 'religion_reminder_id', 'target_type', 'messenger_group_link_id', 'messenger_user_id', 'is_active', 'updated_at'], $sortable),
            'reminder_runs' => $this->columns(['id', 'religion_reminder_id', 'status', 'scheduled_for_utc', 'sent_at', 'attempt_count', 'updated_at'], $sortable),
            default => throw new InvalidArgumentException("Unsupported entity '{$entity}'."),
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $entity, int $id): ?array
    {
        $model = $this->registry->resolveModel($entity);
        $row = $model::query()->find($id);

        return $row?->toArray();
    }

    /**
     * @param  array<int, string>  $columnNames
     * @param  array<int, string>  $sortable
     * @return array<int, array{name:string,label:string,sortable:bool}>
     */
    private function columns(array $columnNames, array $sortable): array
    {
        return array_map(
            static fn (string $name): array => (new TableColumnData(
                name: $name,
                label: Str::headline(str_replace('_', ' ', $name)),
                sortable: in_array($name, $sortable, true),
            ))->toArray(),
            $columnNames
        );
    }

    /**
     * @return array<int, string>
     */
    private function sortableColumns(string $entity): array
    {
        return match ($entity) {
            'religions' => ['id', 'name', 'slug', 'updated_at'],
            'confessions' => ['id', 'name', 'slug', 'updated_at'],
            'rituals' => ['id', 'ritual_key', 'name', 'updated_at'],
            'reminder_types' => ['id', 'type_key', 'name', 'updated_at'],
            'reminder_implementations' => ['id', 'implementation_key', 'implementation_mode', 'updated_at'],
            'religion_commands' => ['id', 'command_key', 'trigger', 'name', 'updated_at'],
            'religion_reminders' => ['id', 'title', 'frequency_mode', 'schedule_timezone', 'updated_at'],
            'language_packs' => ['id', 'code', 'name', 'updated_at'],
            'language_words' => ['id', 'word', 'translation', 'transcription', 'updated_at'],
            'user_group_religion_preferences' => ['id', 'updated_at'],
            'reminder_delivery_targets' => ['id', 'target_type', 'channel_key', 'updated_at'],
            'reminder_runs' => ['id', 'status', 'scheduled_for_utc', 'sent_at', 'attempt_count', 'updated_at'],
            default => ['id'],
        };
    }

    private function supportsActiveFilter(string $entity): bool
    {
        return in_array($entity, [
            'religions',
            'confessions',
            'rituals',
            'reminder_implementations',
            'religion_commands',
            'religion_reminders',
            'language_packs',
            'language_words',
            'user_group_religion_preferences',
            'reminder_delivery_targets',
        ], true);
    }

    /**
     * @return array<int, string>
     */
    private function searchableColumns(string $entity): array
    {
        return match ($entity) {
            'religions' => ['name', 'slug', 'description'],
            'confessions' => ['name', 'slug', 'description'],
            'rituals' => ['ritual_key', 'name', 'description'],
            'reminder_types' => ['type_key', 'name', 'description'],
            'reminder_implementations' => ['implementation_key', 'implementation_mode', 'handler_class'],
            'religion_commands' => ['command_key', 'trigger', 'name', 'description'],
            'religion_reminders' => ['title', 'content_text', 'frequency_mode', 'schedule_timezone', 'command_trigger'],
            'language_packs' => ['code', 'name', 'native_name', 'script'],
            'language_words' => ['word', 'translation', 'transcription', 'phrases'],
            'user_group_religion_preferences' => [],
            'reminder_delivery_targets' => ['target_type', 'channel_key'],
            'reminder_runs' => ['status', 'idempotency_key', 'last_error'],
            default => ['id'],
        };
    }
}
