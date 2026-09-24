<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $content = json_decode(
            (string) file_get_contents(resource_path('content/movie-tier-list.ru.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        DB::table('movie_tier_lists')
            ->where('slug', 'movies')
            ->update([
                'payload' => json_encode([
                    'tiers' => $content['tiers'] ?? [],
                    'movies' => [],
                ], JSON_THROW_ON_ERROR),
                'revision' => DB::raw('revision + 1'),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // The removed starter catalogue is intentionally not restored.
    }
};
