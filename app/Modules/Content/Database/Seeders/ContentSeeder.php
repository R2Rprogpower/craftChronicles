<?php

declare(strict_types=1);

namespace App\Modules\Content\Database\Seeders;

use App\Models\ContentItem;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'Building Products Without Architecture Theatre', 'slug' => 'products-without-architecture-theatre', 'channel' => 'youtube', 'status' => 'planned', 'description' => 'A practical breakdown of architecture choices that improve delivery instead of decorating diagrams.', 'sort_order' => 10],
            ['title' => 'Live Legacy Refactoring', 'slug' => 'live-legacy-refactoring', 'channel' => 'twitch', 'status' => 'planned', 'description' => 'A thematic stream: read an unfamiliar Laravel codebase, map risk, and improve one valuable path.', 'sort_order' => 20],
            ['title' => 'English for Technical Decisions', 'slug' => 'english-for-technical-decisions', 'channel' => 'article', 'status' => 'idea', 'description' => 'Language patterns developers can use to disagree, surface risk, and make trade-offs clearly.', 'sort_order' => 30],
        ];
        foreach ($items as $item) {
            ContentItem::query()->updateOrCreate(['slug' => $item['slug']], $item + ['featured' => true, 'active' => true]);
        }
    }
}
