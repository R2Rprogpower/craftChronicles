<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\NewsFeed\Models\NewsArticle;
use App\Modules\NewsFeed\Models\NewsSource;
use App\Modules\NewsFeed\Models\NewsTopic;
use App\Modules\NewsFeed\Services\NewsFeedImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsFeedFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_lists_topics_and_hides_editor_controls(): void
    {
        config(['movie-tier-list.editor_ips' => ['203.0.113.8']]);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->get('/news-feed')
            ->assertOk()
            ->assertSee('Конечная лента', false)
            ->assertSee('Искусственный интеллект', false)
            ->assertSee('Олег Пономарь: Пояснения', false)
            ->assertDontSee('Добавить новый сайт', false)
            ->assertDontSee('Обновить все источники', false);

        $this->assertDatabaseCount('news_topics', 17);
        $this->assertDatabaseCount('news_sources', 81);
    }

    public function test_editor_can_add_a_public_source(): void
    {
        config(['movie-tier-list.editor_ips' => ['198.51.100.25']]);
        $topic = NewsTopic::query()->firstOrFail();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->post('/news-feed/sources', [
                'news_topic_id' => $topic->id,
                'name' => 'Новый источник',
                'language' => 'uk',
                'site_url' => 'https://example.com/news',
                'feed_url' => 'https://example.com/feed.xml',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('news_sources', [
            'name' => 'Новый источник',
            'language' => 'uk',
        ]);
    }

    public function test_non_editor_cannot_add_or_refresh_sources(): void
    {
        config(['movie-tier-list.editor_ips' => ['203.0.113.8']]);
        $topic = NewsTopic::query()->firstOrFail();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->post('/news-feed/sources', [
                'news_topic_id' => $topic->id,
                'name' => 'Запрещённый источник',
                'language' => 'ru',
                'site_url' => 'https://example.com/',
            ])
            ->assertForbidden();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->post('/news-feed/refresh')
            ->assertForbidden();

        $this->assertDatabaseMissing('news_sources', ['name' => 'Запрещённый источник']);
    }

    public function test_private_source_url_is_rejected(): void
    {
        config(['movie-tier-list.editor_ips' => ['198.51.100.25']]);
        $topic = NewsTopic::query()->firstOrFail();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
            ->post('/news-feed/sources', [
                'news_topic_id' => $topic->id,
                'name' => 'Локальный адрес',
                'language' => 'ru',
                'site_url' => 'http://127.0.0.1/internal',
            ])
            ->assertSessionHasErrors('site_url');

        $this->assertDatabaseMissing('news_sources', ['name' => 'Локальный адрес']);
    }

    public function test_importer_discovers_feed_saves_history_and_deduplicates_links(): void
    {
        $source = NewsSource::query()->firstOrFail();
        $source->update([
            'site_url' => 'http://93.184.216.34/news/',
            'feed_url' => null,
        ]);

        Http::fake([
            'http://93.184.216.34/news/' => Http::response(
                '<html><head><link rel="alternate" type="application/rss+xml" href="/feed.xml"></head></html>',
                200,
                ['Content-Type' => 'text/html'],
            ),
            'http://93.184.216.34/feed.xml' => Http::response($this->rssFixture(), 200, ['Content-Type' => 'application/rss+xml']),
        ]);

        $importer = app(NewsFeedImporter::class);
        $first = $importer->import($source->id);
        $second = $importer->import($source->id);

        $this->assertSame(2, $first['added']);
        $this->assertSame(0, $second['added']);
        $this->assertDatabaseCount('news_articles', 2);
        $this->assertDatabaseHas('news_articles', ['title' => 'Первая публикация']);

        $source->refresh();
        $this->assertSame('http://93.184.216.34/feed.xml', $source->feed_url);
        $this->assertNull($source->last_error);
        $this->assertNotNull($source->last_success_at);
    }

    public function test_history_can_be_filtered_by_topic_source_language_and_period(): void
    {
        $source = NewsSource::query()->where('language', 'uk')->firstOrFail();
        NewsArticle::query()->create([
            'news_source_id' => $source->id,
            'title' => 'Свежая тестовая публикация',
            'url' => 'https://example.com/fresh',
            'url_hash' => hash('sha256', 'https://example.com/fresh'),
            'published_at' => now()->subHour(),
            'discovered_at' => now(),
        ]);
        NewsArticle::query()->create([
            'news_source_id' => $source->id,
            'title' => 'Старая тестовая публикация',
            'url' => 'https://example.com/old',
            'url_hash' => hash('sha256', 'https://example.com/old'),
            'published_at' => now()->subDays(3),
            'discovered_at' => now()->subDays(3),
        ]);

        $this->get('/news-feed?topic='.$source->topic->slug.'&source='.$source->id.'&language=uk&period=12h&q=Свежая')
            ->assertOk()
            ->assertSee('Свежая тестовая публикация', false)
            ->assertDontSee('Старая тестовая публикация', false);

        $this->get('/news-feed?period=all')
            ->assertOk()
            ->assertSee('Свежая тестовая публикация', false)
            ->assertSee('Старая тестовая публикация', false);
    }

    private function rssFixture(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <rss version="2.0"><channel><title>Test</title>
              <item><title>Первая публикация</title><link>https://example.com/articles/one#comments</link><pubDate>Sun, 25 Oct 2026 08:00:00 GMT</pubDate></item>
              <item><title>Вторая публикация</title><link>https://example.com/articles/two</link><pubDate>Sun, 25 Oct 2026 09:00:00 GMT</pubDate></item>
            </channel></rss>
            XML;
    }
}
