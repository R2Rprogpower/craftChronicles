<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\MovieTierList\Models\MovieTierList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovieTierListFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_everyone_can_view_the_movie_tier_list_in_read_only_mode(): void
    {
        config(['movie-tier-list.editor_ips' => ['203.0.113.8']]);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->get('/movies-tier-list')
            ->assertOk()
            ->assertSee('Кино — личный tier list', false)
            ->assertSee('Только просмотр', false)
            ->assertSee('data-can-edit="false"', false)
            ->assertDontSee('+ Добавить фильм', false);
    }

    public function test_editor_ip_sees_editing_controls(): void
    {
        config(['movie-tier-list.editor_ips' => ['198.51.100.25']]);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->get('/movies-tier-list')
            ->assertOk()
            ->assertSee('Режим редактора', false)
            ->assertSee('data-can-edit="true"', false)
            ->assertSee('+ Добавить фильм', false);
    }

    public function test_hashed_editor_ip_can_edit_without_exposing_the_address_in_config(): void
    {
        config([
            'movie-tier-list.editor_ips' => [],
            'movie-tier-list.editor_ip_hashes' => [hash('sha256', '198.51.100.25')],
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->get('/movies-tier-list')
            ->assertOk()
            ->assertSee('Режим редактора', false);
    }

    public function test_non_editor_ip_cannot_write_even_with_a_forged_request(): void
    {
        config(['movie-tier-list.editor_ips' => ['203.0.113.8']]);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->putJson('/movies-tier-list', $this->payload())
            ->assertForbidden();
    }

    public function test_empty_allowlist_denies_all_writes(): void
    {
        config([
            'movie-tier-list.editor_ips' => [],
            'movie-tier-list.editor_ip_hashes' => [],
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->putJson('/movies-tier-list', $this->payload())
            ->assertForbidden();
    }

    public function test_editor_can_save_a_shared_rating(): void
    {
        config(['movie-tier-list.editor_ips' => ['198.51.100.0/24']]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->putJson('/movies-tier-list', $this->payload());

        $response->assertOk()
            ->assertJsonPath('revision', 2)
            ->assertJsonPath('message', 'Сохранено');

        $tierList = MovieTierList::query()->where('slug', 'movies')->firstOrFail();

        $this->assertSame('S', $tierList->payload['movies'][0]['tier']);
        $this->assertSame(2, $tierList->revision);
    }

    public function test_stale_revision_is_rejected(): void
    {
        config(['movie-tier-list.editor_ips' => ['198.51.100.25']]);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->putJson('/movies-tier-list', $this->payload())
            ->assertOk();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->putJson('/movies-tier-list', $this->payload())
            ->assertConflict();
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return [
            'revision' => 1,
            'movies' => [[
                'id' => 'the-godfather',
                'title' => 'Крёстный отец',
                'year' => 1972,
                'genre' => 'Драма',
                'poster_url' => null,
                'tier' => 'S',
                'position' => 0,
            ]],
        ];
    }
}
