<?php

declare(strict_types=1);

namespace App\Modules\MovieTierList\Services;

use App\Modules\MovieTierList\Models\MovieTierList;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MovieTierListService
{
    public const SLUG = 'movies';

    public function __construct(private readonly MovieTierListContent $content) {}

    public function get(): MovieTierList
    {
        $content = $this->content->get();

        return MovieTierList::query()->firstOrCreate(
            ['slug' => self::SLUG],
            [
                'payload' => [
                    'tiers' => $content['tiers'] ?? [],
                    'movies' => $content['initial_movies'] ?? [],
                ],
                'revision' => 1,
            ],
        );
    }

    /**
     * @param  list<array<string, mixed>>  $tiers
     * @param  list<array<string, mixed>>  $movies
     */
    public function replace(array $tiers, array $movies, int $expectedRevision): MovieTierList
    {
        return DB::transaction(function () use ($tiers, $movies, $expectedRevision): MovieTierList {
            $tierList = MovieTierList::query()
                ->where('slug', self::SLUG)
                ->lockForUpdate()
                ->first();

            if ($tierList === null) {
                $tierList = $this->get();
            }

            if ($tierList->revision !== $expectedRevision) {
                throw new RuntimeException('The movie tier list was changed in another session.');
            }

            $tierList->payload = ['tiers' => $tiers, 'movies' => $movies];
            $tierList->revision++;
            $tierList->save();

            return $tierList->refresh();
        }, 3);
    }
}
