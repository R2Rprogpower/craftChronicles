<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\GratitudeVariants\Models\GratitudeVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GratitudeVariantsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_contains_seeded_variants_and_filters(): void
    {
        config(['movie-tier-list.editor_ips' => ['203.0.113.8']]);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->get('/gratitude-variants')
            ->assertOk()
            ->assertSee('Благодарность Единому', false)
            ->assertSee('Случайный вариант', false)
            ->assertSee('Все языки', false)
            ->assertSee('id="variantList"', false)
            ->assertSee('data-can-edit="false"', false)
            ->assertDontSee('id="addForm"', false)
            ->assertDontSee('id="edit"', false);

        $this->assertDatabaseCount('gratitude_variants', 8);
        $this->assertDatabaseHas('gratitude_variants', ['title' => 'Вариант 1 — Русский']);
        $this->assertDatabaseHas('gratitude_variants', ['title' => 'Варіант 2 — Українська']);
    }

    public function test_editor_can_add_a_variant_with_normalized_tags(): void
    {
        config(['movie-tier-list.editor_ips' => ['198.51.100.25']]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->postJson('/gratitude-variants', [
                'title' => 'Утренний вариант',
                'language' => 'ru',
                'body' => 'Благодарю за новый день, ясность и возможность созидать.',
                'tags' => ['#Утро', 'творчество', 'утро'],
            ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Вариант добавлен')
            ->assertJsonPath('variant.tags', ['утро', 'творчество']);

        $variant = GratitudeVariant::query()->where('title', 'Утренний вариант')->firstOrFail();
        $this->assertSame(['утро', 'творчество'], $variant->tags);
    }

    public function test_editor_can_update_a_variant_with_normalized_tags(): void
    {
        config(['movie-tier-list.editor_ips' => ['198.51.100.25']]);
        $variant = GratitudeVariant::query()->firstOrFail();

        $response = $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->putJson("/gratitude-variants/{$variant->id}", [
                'title' => 'Обновлённый вариант',
                'language' => 'uk',
                'body' => 'Дякую за можливість змінювати, вдосконалювати й творити.',
                'tags' => ['#Зміни', 'Творчість', 'зміни'],
            ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Вариант обновлён')
            ->assertJsonPath('variant.title', 'Обновлённый вариант')
            ->assertJsonPath('variant.tags', ['зміни', 'творчість']);

        $variant->refresh();
        $this->assertSame('Обновлённый вариант', $variant->title);
        $this->assertSame('uk', $variant->language);
        $this->assertSame(['зміни', 'творчість'], $variant->tags);
    }

    public function test_non_editor_cannot_add_update_or_delete_variants(): void
    {
        config(['movie-tier-list.editor_ips' => ['203.0.113.8']]);
        $variant = GratitudeVariant::query()->firstOrFail();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->postJson('/gratitude-variants', [
                'title' => 'Запрещённый вариант',
                'language' => 'ru',
                'body' => 'Этот вариант не должен сохраниться в базе данных.',
                'tags' => [],
            ])
            ->assertForbidden();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->putJson("/gratitude-variants/{$variant->id}", [
                'title' => 'Запрещённое изменение',
                'language' => 'ru',
                'body' => 'Этот текст не должен заменить существующий вариант.',
                'tags' => [],
            ])
            ->assertForbidden();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->deleteJson("/gratitude-variants/{$variant->id}")
            ->assertForbidden();

        $this->assertDatabaseCount('gratitude_variants', 8);
    }

    public function test_editor_can_delete_a_variant(): void
    {
        config(['movie-tier-list.editor_ips' => ['198.51.100.25']]);
        $variant = GratitudeVariant::query()->firstOrFail();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->deleteJson("/gratitude-variants/{$variant->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Вариант удалён');

        $this->assertModelMissing($variant);
    }

    public function test_new_variant_is_validated(): void
    {
        config(['movie-tier-list.editor_ips' => ['198.51.100.25']]);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->postJson('/gratitude-variants', [
                'title' => '',
                'language' => 'fr',
                'body' => 'short',
                'tags' => ['invalid tag'],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'language', 'body', 'tags.0']);
    }
}
