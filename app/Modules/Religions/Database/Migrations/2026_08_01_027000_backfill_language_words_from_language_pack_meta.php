<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $packs = DB::table('language_packs')->select(['id', 'meta_json'])->get();

        foreach ($packs as $pack) {
            $meta = $pack->meta_json;

            if (is_string($meta)) {
                $decoded = json_decode($meta, true);
                $meta = is_array($decoded) ? $decoded : null;
            }

            if (! is_array($meta)) {
                continue;
            }

            $words = $meta['words'] ?? null;
            if (! is_array($words)) {
                continue;
            }

            foreach ($words as $entry) {
                if (! is_array($entry)) {
                    continue;
                }

                $word = trim((string) ($entry['word'] ?? ''));
                $translation = trim((string) ($entry['translation'] ?? ''));
                $transcription = trim((string) ($entry['transcription'] ?? ''));
                $phrases = trim((string) ($entry['phrases'] ?? ''));

                if ($word === '' || $translation === '' || $transcription === '') {
                    continue;
                }

                DB::table('language_words')->updateOrInsert(
                    [
                        'language_pack_id' => (int) $pack->id,
                        'word' => $word,
                        'translation' => $translation,
                    ],
                    [
                        'transcription' => $transcription,
                        'phrases' => $phrases !== '' ? $phrases : null,
                        'meta_json' => null,
                        'is_active' => true,
                        'updated_at' => now('UTC'),
                        'created_at' => now('UTC'),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        // Intentional no-op to avoid deleting user-managed words.
    }
};
