<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_topics', function (Blueprint $table): void {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name', 160);
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('news_sources', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('news_topic_id')->constrained('news_topics')->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('language', 2)->index();
            $table->text('site_url');
            $table->text('feed_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });

        Schema::create('news_articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('news_source_id')->constrained('news_sources')->cascadeOnDelete();
            $table->string('title', 600);
            $table->text('url');
            $table->char('url_hash', 64)->unique();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('discovered_at')->index();
            $table->timestamps();
            $table->index(['news_source_id', 'published_at']);
        });

        $content = json_decode(
            (string) file_get_contents(resource_path('content/news-feed-sources.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $now = now();

        foreach ($content['topics'] as $topic) {
            DB::table('news_topics')->insert([
                ...$topic,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $topicIds = DB::table('news_topics')->pluck('id', 'slug');

        foreach ($content['sources'] as $source) {
            $topic = $source['topic'];
            unset($source['topic']);

            DB::table('news_sources')->insert([
                ...$source,
                'news_topic_id' => $topicIds[$topic],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
        Schema::dropIfExists('news_sources');
        Schema::dropIfExists('news_topics');
    }
};
