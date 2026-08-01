<?php

declare(strict_types=1);

namespace App\Modules\Religions\Services;

use App\Modules\Religions\Models\Confession;
use App\Modules\Religions\Models\LanguagePack;
use App\Modules\Religions\Models\LanguageWord;
use App\Modules\Religions\Models\Religion;
use App\Modules\Religions\Models\ReligionReminder;
use App\Modules\Religions\Models\ReminderType;
use App\Modules\Religions\Models\Ritual;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReligionBundleTextService
{
    public function exportForConfession(int $confessionId): string
    {
        $confession = DB::table('confessions')
            ->where('id', $confessionId)
            ->first(['id', 'religion_id', 'name', 'slug', 'description', 'description_format', 'welcome_message', 'language_pack_id', 'is_active']);

        if ($confession === null) {
            throw new InvalidArgumentException('Confession not found for export.');
        }

        $religion = DB::table('religions')
            ->where('id', (int) $confession->religion_id)
            ->first(['id', 'name', 'slug', 'description', 'description_format', 'is_active']);

        if ($religion === null) {
            throw new InvalidArgumentException('Confession has no religion relation.');
        }

        $confessionIdValue = (int) $confession->id;

        $confessionLanguageCode = DB::table('language_packs')
            ->where('id', (int) ($confession->language_pack_id ?? 0))
            ->value('code');

        $confessionLanguageCode = is_string($confessionLanguageCode) && trim($confessionLanguageCode) !== ''
            ? $confessionLanguageCode
            : 'en';

        $rituals = DB::table('rituals')
            ->where('confession_id', $confessionIdValue)
            ->orderBy('id')
            ->get(['ritual_key', 'name', 'description', 'description_format', 'is_active']);

        $reminders = DB::table('religion_reminders as rr')
            ->leftJoin('language_packs as lp', 'lp.id', '=', 'rr.language_pack_id')
            ->leftJoin('reminder_types as rt', 'rt.id', '=', 'rr.reminder_type_id')
            ->where('rr.confession_id', $confessionIdValue)
            ->orderBy('rr.id')
            ->get([
                'rr.title',
                'rr.content_text',
                'rr.text_format',
                'rr.frequency_mode',
                'rr.schedule_preset',
                'rr.interval_minutes',
                'rr.schedule_time',
                'rr.schedule_timezone',
                'rr.schedule_weekday',
                'rr.schedule_monthday',
                'rr.schedule_year_month',
                'rr.schedule_year_day',
                'rr.frequency_value',
                'rr.command_trigger',
                'rr.word_mode',
                'rr.is_active',
                'lp.code as language_code',
                'rt.type_key as reminder_type_key',
            ]);

        $languageCodes = ['en', $confessionLanguageCode];

        foreach ($reminders as $reminder) {
            if (is_string($reminder->language_code ?? null) && trim((string) $reminder->language_code) !== '') {
                $languageCodes[] = (string) $reminder->language_code;
            }
        }

        $wordRows = DB::table('language_words as lw')
            ->join('language_packs as lp', 'lp.id', '=', 'lw.language_pack_id')
            ->whereIn('lp.code', $languageCodes)
            ->orderBy('lw.id')
            ->get([
                'lp.code as language_code',
                'lw.word',
                'lw.translation',
                'lw.transcription',
                'lw.phrases',
                'lw.is_active',
            ]);

        $wordsByLanguageCode = [];
        foreach ($wordRows as $row) {
            $code = trim((string) ($row->language_code ?? ''));
            if ($code === '') {
                continue;
            }

            $wordsByLanguageCode[$code] ??= [];
            $wordsByLanguageCode[$code][] = [
                'word' => (string) ($row->word ?? ''),
                'translation' => (string) ($row->translation ?? ''),
                'transcription' => (string) ($row->transcription ?? ''),
                'phrases' => (string) ($row->phrases ?? ''),
            ];
        }

        $wordsByLanguageCode['en'] = $this->mergeWithDefaultSampleWords($wordsByLanguageCode['en'] ?? []);

        $languageCodes = array_values(array_unique(array_filter($languageCodes, static fn (string $code): bool => trim($code) !== '')));

        $languagePacks = DB::table('language_packs')
            ->whereIn('code', $languageCodes)
            ->orderBy('code')
            ->get(['code', 'name', 'native_name', 'script', 'is_active', 'meta_json']);

        $lines = [];
        $lines[] = '# Sectioned text bundle for import/export';
        $lines[] = '# Generated from confession id '.$confessionIdValue;
        $lines[] = '';

        $lines = array_merge($lines, $this->renderSection('RELIGION', [
            'name' => (string) ($religion->name ?? ''),
            'slug' => (string) ($religion->slug ?? ''),
            'description' => (string) ($religion->description ?? ''),
            'description_format' => (string) ($religion->description_format ?? 'plain'),
            'is_active' => (bool) ($religion->is_active ?? false) ? '1' : '0',
        ]));

        $lines = array_merge($lines, $this->renderSection('CONFESSION', [
            'name' => (string) ($confession->name ?? ''),
            'slug' => (string) ($confession->slug ?? ''),
            'description' => (string) ($confession->description ?? ''),
            'description_format' => (string) ($confession->description_format ?? 'plain'),
            'welcome_message' => (string) ($confession->welcome_message ?? ''),
            'language_code' => $confessionLanguageCode,
            'is_active' => (bool) ($confession->is_active ?? false) ? '1' : '0',
        ]));

        $languageBlocks = [];
        foreach ($languagePacks as $pack) {
            $code = (string) ($pack->code ?? '');
            $wordsJson = (string) json_encode($wordsByLanguageCode[$code] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($wordsJson === '' || $wordsJson === 'null') {
                $wordsJson = $this->extractWordsJsonFromMeta($pack->meta_json ?? null);
            }

            $languageBlocks[] = [
                'code' => $code,
                'name' => (string) $pack->name,
                'native_name' => (string) ($pack->native_name ?? ''),
                'script' => (string) ($pack->script ?? ''),
                'words_json' => $wordsJson,
                'is_active' => $pack->is_active ? '1' : '0',
            ];
        }

        if ($languageBlocks === []) {
            $languageBlocks[] = [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'script' => 'Latin',
                'words_json' => (string) json_encode($this->defaultSampleWords(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active' => '1',
            ];
        }

        $lines = array_merge($lines, $this->renderBlockSection('LANGUAGES', $languageBlocks));

        $ritualBlocks = [];
        foreach ($rituals as $ritual) {
            $ritualBlocks[] = [
                'ritual_key' => (string) $ritual->ritual_key,
                'name' => (string) $ritual->name,
                'description' => (string) ($ritual->description ?? ''),
                'description_format' => (string) ($ritual->description_format ?? 'plain'),
                'is_active' => $ritual->is_active ? '1' : '0',
            ];
        }

        $lines = array_merge($lines, $this->renderBlockSection('RITUALS', $ritualBlocks));

        $reminderBlocks = [];
        foreach ($reminders as $reminder) {
            $reminderBlocks[] = [
                'title' => (string) ($reminder->title ?? ''),
                'content_text' => (string) ($reminder->content_text ?? ''),
                'text_format' => (string) ($reminder->text_format ?? 'plain'),
                'frequency_mode' => (string) ($reminder->frequency_mode ?? 'command'),
                'schedule_preset' => (string) ($reminder->schedule_preset ?? 'command'),
                'interval_minutes' => (string) ($reminder->interval_minutes ?? ''),
                'schedule_time' => (string) ($reminder->schedule_time ?? ''),
                'schedule_timezone' => (string) ($reminder->schedule_timezone ?? 'Europe/Kyiv'),
                'schedule_weekday' => (string) ($reminder->schedule_weekday ?? ''),
                'schedule_monthday' => (string) ($reminder->schedule_monthday ?? ''),
                'schedule_year_month' => (string) ($reminder->schedule_year_month ?? ''),
                'schedule_year_day' => (string) ($reminder->schedule_year_day ?? ''),
                'frequency_value' => (string) ($reminder->frequency_value ?? ''),
                'command_trigger' => (string) ($reminder->command_trigger ?? ''),
                'language_code' => (string) ($reminder->language_code ?? $confessionLanguageCode),
                'word_mode' => (bool) ($reminder->word_mode ?? false) ? '1' : '0',
                'reminder_type_key' => (string) ($reminder->reminder_type_key ?? 'custom'),
                'is_active' => (bool) ($reminder->is_active ?? false) ? '1' : '0',
            ];
        }

        $lines = array_merge($lines, $this->renderBlockSection('REMINDERS', $reminderBlocks));

        $wordBlocks = [];
        foreach ($wordsByLanguageCode as $code => $entries) {
            foreach ($entries as $entry) {
                $wordBlocks[] = [
                    'language_code' => (string) $code,
                    'word' => (string) ($entry['word'] ?? ''),
                    'translation' => (string) ($entry['translation'] ?? ''),
                    'transcription' => (string) ($entry['transcription'] ?? ''),
                    'phrases' => (string) ($entry['phrases'] ?? ''),
                    'is_active' => '1',
                ];
            }
        }

        if ($wordBlocks === []) {
            foreach ($this->defaultSampleWords() as $entry) {
                $wordBlocks[] = [
                    'language_code' => 'en',
                    'word' => $entry['word'],
                    'translation' => $entry['translation'],
                    'transcription' => $entry['transcription'],
                    'phrases' => $entry['phrases'],
                    'is_active' => '1',
                ];
            }
        }

        $lines = array_merge($lines, $this->renderBlockSection('WORDS', $wordBlocks));

        return implode("\n", $lines)."\n";
    }

    /**
     * @return array{religion_id:int,confession_id:int,languages:int,rituals:int,reminders:int,words:int}
     */
    public function importFromText(string $text): array
    {
        $sections = $this->parseSections($text);

        $religionData = $this->parseAssocSection($sections, 'RELIGION');
        $confessionData = $this->parseAssocSection($sections, 'CONFESSION');
        $languageBlocks = $this->parseBlockSection($sections, 'LANGUAGES');
        $ritualBlocks = $this->parseBlockSection($sections, 'RITUALS');
        $reminderBlocks = $this->parseBlockSection($sections, 'REMINDERS');
        $wordBlocks = $this->parseBlockSection($sections, 'WORDS');

        if (($religionData['name'] ?? '') === '' || ($religionData['slug'] ?? '') === '') {
            throw new InvalidArgumentException('RELIGION section must contain name and slug.');
        }

        if (($confessionData['name'] ?? '') === '' || ($confessionData['slug'] ?? '') === '') {
            throw new InvalidArgumentException('CONFESSION section must contain name and slug.');
        }

        return DB::transaction(function () use ($religionData, $confessionData, $languageBlocks, $ritualBlocks, $reminderBlocks, $wordBlocks): array {
            $languageCodeToId = [];

            foreach ($languageBlocks as $block) {
                $code = trim((string) ($block['code'] ?? ''));
                if ($code === '') {
                    continue;
                }

                $pack = LanguagePack::query()->updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => (string) ($block['name'] ?? $code),
                        'native_name' => (string) ($block['native_name'] ?? ''),
                        'script' => (string) ($block['script'] ?? ''),
                        'meta_json' => $this->buildLanguageMetaFromBlock($block),
                        'is_active' => $this->toBool($block['is_active'] ?? '1'),
                    ]
                );

                $languageCodeToId[$code] = (int) $pack->getKey();
            }

            if (! isset($languageCodeToId['en'])) {
                $english = LanguagePack::query()->updateOrCreate(
                    ['code' => 'en'],
                    [
                        'name' => 'English',
                        'native_name' => 'English',
                        'script' => 'Latin',
                        'meta_json' => ['words' => [['word' => 'shalom', 'translation' => 'peace', 'transcription' => 'sha-lom']]],
                        'is_active' => true,
                    ]
                );

                $languageCodeToId['en'] = (int) $english->getKey();
            }

            // Backward-compatible: if WORDS section is not provided, seed from LANGUAGES.words_json.
            if ($wordBlocks === []) {
                foreach ($languageBlocks as $block) {
                    $legacyCode = trim((string) ($block['code'] ?? ''));
                    if ($legacyCode === '') {
                        continue;
                    }

                    foreach ($this->parseWordsJson((string) ($block['words_json'] ?? '')) as $entry) {
                        $entry['language_code'] = $legacyCode;
                        $wordBlocks[] = $entry;
                    }
                }
            }

            $religion = Religion::query()->updateOrCreate(
                ['slug' => (string) $religionData['slug']],
                [
                    'name' => (string) $religionData['name'],
                    'description' => (string) ($religionData['description'] ?? ''),
                    'description_format' => (string) ($religionData['description_format'] ?? 'plain'),
                    'is_active' => $this->toBool($religionData['is_active'] ?? '1'),
                ]
            );

            $confessionLanguageCode = trim((string) ($confessionData['language_code'] ?? 'en'));
            $confessionLanguageId = $languageCodeToId[$confessionLanguageCode] ?? $languageCodeToId['en'];

            $confession = Confession::query()->updateOrCreate(
                [
                    'religion_id' => (int) $religion->getKey(),
                    'slug' => (string) $confessionData['slug'],
                ],
                [
                    'name' => (string) $confessionData['name'],
                    'description' => (string) ($confessionData['description'] ?? ''),
                    'description_format' => (string) ($confessionData['description_format'] ?? 'plain'),
                    'welcome_message' => (string) ($confessionData['welcome_message'] ?? ''),
                    'language_pack_id' => $confessionLanguageId,
                    'is_active' => $this->toBool($confessionData['is_active'] ?? '1'),
                ]
            );

            foreach ($ritualBlocks as $block) {
                $ritualKey = trim((string) ($block['ritual_key'] ?? ''));
                if ($ritualKey === '') {
                    continue;
                }

                Ritual::query()->updateOrCreate(
                    [
                        'confession_id' => (int) $confession->getKey(),
                        'ritual_key' => $ritualKey,
                    ],
                    [
                        'name' => (string) ($block['name'] ?? $ritualKey),
                        'description' => (string) ($block['description'] ?? ''),
                        'description_format' => (string) ($block['description_format'] ?? 'plain'),
                        'is_active' => $this->toBool($block['is_active'] ?? '1'),
                    ]
                );
            }

            foreach ($reminderBlocks as $block) {
                $title = trim((string) ($block['title'] ?? ''));
                if ($title === '') {
                    continue;
                }

                $reminderTypeKey = trim((string) ($block['reminder_type_key'] ?? 'custom'));
                $reminderType = ReminderType::query()->firstOrCreate(
                    ['type_key' => $reminderTypeKey],
                    [
                        'name' => ucfirst(str_replace('-', ' ', $reminderTypeKey)),
                        'description' => 'Imported from text bundle',
                        'is_builtin' => false,
                    ]
                );

                $languageCode = trim((string) ($block['language_code'] ?? $confessionLanguageCode));
                $reminderLanguageId = $languageCodeToId[$languageCode] ?? $confessionLanguageId;

                ReligionReminder::query()->updateOrCreate(
                    [
                        'confession_id' => (int) $confession->getKey(),
                        'title' => $title,
                    ],
                    [
                        'ritual_id' => null,
                        'reminder_type_id' => (int) $reminderType->getKey(),
                        'implementation_id' => null,
                        'command_id' => null,
                        'language_pack_id' => $reminderLanguageId,
                        'word_mode' => $this->toBool($block['word_mode'] ?? '0'),
                        'content_text' => (string) ($block['content_text'] ?? ''),
                        'text_format' => (string) ($block['text_format'] ?? 'plain'),
                        'frequency_mode' => (string) ($block['frequency_mode'] ?? 'command'),
                        'schedule_preset' => (string) ($block['schedule_preset'] ?? 'command'),
                        'interval_minutes' => $this->toNullableInt($block['interval_minutes'] ?? null),
                        'schedule_time' => $this->toNullableString($block['schedule_time'] ?? null),
                        'schedule_timezone' => $this->toNullableString($block['schedule_timezone'] ?? 'Europe/Kyiv') ?? 'Europe/Kyiv',
                        'schedule_weekday' => $this->toNullableString($block['schedule_weekday'] ?? null),
                        'schedule_monthday' => $this->toNullableInt($block['schedule_monthday'] ?? null),
                        'schedule_year_month' => $this->toNullableInt($block['schedule_year_month'] ?? null),
                        'schedule_year_day' => $this->toNullableInt($block['schedule_year_day'] ?? null),
                        'frequency_value' => $this->toNullableString($block['frequency_value'] ?? null),
                        'command_trigger' => $this->toNullableString($block['command_trigger'] ?? null),
                        'meta_json' => null,
                        'is_active' => $this->toBool($block['is_active'] ?? '1'),
                    ]
                );
            }

            foreach ($wordBlocks as $block) {
                $languageCode = trim((string) ($block['language_code'] ?? ''));
                if ($languageCode === '') {
                    $languageCode = 'en';
                }

                $languagePackId = $languageCodeToId[$languageCode] ?? $languageCodeToId['en'];
                if ($languagePackId <= 0) {
                    continue;
                }

                $word = trim((string) ($block['word'] ?? ''));
                $translation = trim((string) ($block['translation'] ?? ''));
                $transcription = trim((string) ($block['transcription'] ?? ''));

                if ($word === '' || $translation === '' || $transcription === '') {
                    continue;
                }

                LanguageWord::query()->updateOrCreate(
                    [
                        'language_pack_id' => $languagePackId,
                        'word' => $word,
                        'translation' => $translation,
                    ],
                    [
                        'transcription' => $transcription,
                        'phrases' => $this->toNullableString($block['phrases'] ?? null),
                        'meta_json' => null,
                        'is_active' => $this->toBool($block['is_active'] ?? '1'),
                    ]
                );
            }

            return [
                'religion_id' => (int) $religion->getKey(),
                'confession_id' => (int) $confession->getKey(),
                'languages' => count($languageBlocks),
                'rituals' => count($ritualBlocks),
                'reminders' => count($reminderBlocks),
                'words' => count($wordBlocks),
            ];
        });
    }

    /**
     * @param  array<string, array<int, string>>  $sections
     * @return array<string, string>
     */
    private function parseAssocSection(array $sections, string $name): array
    {
        return $this->parseAssocLines($sections[$name] ?? []);
    }

    /**
     * @param  array<string, array<int, string>>  $sections
     * @return array<int, array<string, string>>
     */
    private function parseBlockSection(array $sections, string $name): array
    {
        $lines = $sections[$name] ?? [];
        if ($lines === []) {
            return [];
        }

        $blocks = [];
        $current = [];

        foreach ($lines as $line) {
            if (trim($line) === '---') {
                if ($current !== []) {
                    $blocks[] = $this->parseAssocLines($current);
                    $current = [];
                }

                continue;
            }

            $current[] = $line;
        }

        if ($current !== []) {
            $blocks[] = $this->parseAssocLines($current);
        }

        return array_values(array_filter($blocks, static fn (array $block): bool => $block !== []));
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function parseSections(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $sections = [];
        $current = null;

        foreach ($lines as $rawLine) {
            $line = rtrim($rawLine);

            if ($line === '' || str_starts_with(trim($line), '#')) {
                continue;
            }

            if (preg_match('/^\[([A-Z_]+)\]$/', trim($line), $matches) === 1) {
                $current = $matches[1];
                $sections[$current] ??= [];
                continue;
            }

            if ($current === null) {
                continue;
            }

            $sections[$current][] = $line;
        }

        return $sections;
    }

    /**
     * @param  array<int, string>  $lines
     * @return array<string, string>
     */
    private function parseAssocLines(array $lines): array
    {
        $assoc = [];

        foreach ($lines as $line) {
            $trim = trim($line);

            if ($trim === '' || $trim === '---' || str_starts_with($trim, '#')) {
                continue;
            }

            $parts = explode(':', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim((string) $parts[0]);
            $value = trim((string) $parts[1]);

            if ($key === '') {
                continue;
            }

            $assoc[$key] = $value;
        }

        return $assoc;
    }

    /**
     * @param  array<string, string>  $values
     * @return array<int, string>
     */
    private function renderSection(string $name, array $values): array
    {
        $lines = ['['.$name.']'];

        foreach ($values as $key => $value) {
            $lines[] = $key.': '.$this->sanitizeValue($value);
        }

        $lines[] = '';

        return $lines;
    }

    /**
     * @param  array<int, array<string, string>>  $blocks
     * @return array<int, string>
     */
    private function renderBlockSection(string $name, array $blocks): array
    {
        $lines = ['['.$name.']'];

        foreach ($blocks as $index => $block) {
            if ($index > 0) {
                $lines[] = '---';
            }

            foreach ($block as $key => $value) {
                $lines[] = $key.': '.$this->sanitizeValue($value);
            }
        }

        $lines[] = '';

        return $lines;
    }

    private function sanitizeValue(string $value): string
    {
        return str_replace(["\r", "\n"], [' ', ' '], trim($value));
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'y', 'on'], true);
    }

    private function toNullableInt(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $stringValue = trim((string) $value);
        if ($stringValue === '' || ! is_numeric($stringValue)) {
            return null;
        }

        return (int) $stringValue;
    }

    private function toNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $stringValue = trim((string) $value);

        return $stringValue === '' ? null : $stringValue;
    }

    /**
     * @param  array<string, string>  $block
     * @return array<string, mixed>
     */
    private function buildLanguageMetaFromBlock(array $block): array
    {
        $raw = trim((string) ($block['words_json'] ?? ''));
        if ($raw === '') {
            return ['words' => []];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return ['words' => []];
        }

        if (! is_array($decoded)) {
            return ['words' => []];
        }

        $words = array_values(array_filter($decoded, static function (mixed $entry): bool {
            if (! is_array($entry)) {
                return false;
            }

            $word = trim((string) ($entry['word'] ?? ''));
            $translation = trim((string) ($entry['translation'] ?? ''));
            $transcription = trim((string) ($entry['transcription'] ?? ''));

            return $word !== '' && $translation !== '' && $transcription !== '';
        }));

        return ['words' => $words];
    }

    private function extractWordsJsonFromMeta(mixed $meta): string
    {
        if (is_string($meta)) {
            try {
                $meta = json_decode($meta, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable) {
                return '[]';
            }
        }

        if (! is_array($meta)) {
            return '[]';
        }

        $words = $meta['words'] ?? [];
        if (! is_array($words)) {
            return '[]';
        }

        return (string) json_encode($words, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return array<int, array{word:string,translation:string,transcription:string,phrases:string}>
     */
    private function parseWordsJson(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return [];
        }

        if (! is_array($decoded)) {
            return [];
        }

        $result = [];

        foreach ($decoded as $entry) {
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

            $result[] = [
                'word' => $word,
                'translation' => $translation,
                'transcription' => $transcription,
                'phrases' => $phrases,
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array{word:string,translation:string,transcription:string,phrases:string}>
     */
    private function defaultSampleWords(): array
    {
        return [
            ['word' => 'shalom', 'translation' => 'peace', 'transcription' => 'sha-lom', 'phrases' => 'Shalom aleichem'],
            ['word' => 'chesed', 'translation' => 'kindness', 'transcription' => 'khe-sed', 'phrases' => 'Act with chesed daily'],
            ['word' => 'emunah', 'translation' => 'faith', 'transcription' => 'eh-moo-nah', 'phrases' => 'Hold emunah in hard times'],
            ['word' => 'simcha', 'translation' => 'joy', 'transcription' => 'sim-kha', 'phrases' => 'Serve with simcha'],
            ['word' => 'tzedek', 'translation' => 'justice', 'transcription' => 'tze-dek', 'phrases' => 'Pursue tzedek always'],
            ['word' => 'rachamim', 'translation' => 'compassion', 'transcription' => 'ra-kha-mim', 'phrases' => 'Show rachamim to others'],
            ['word' => 'todah', 'translation' => 'thanks', 'transcription' => 'to-dah', 'phrases' => 'Say todah every day'],
            ['word' => 'or', 'translation' => 'light', 'transcription' => 'or', 'phrases' => 'Be a light for others'],
        ];
    }

    /**
     * @param  array<int, array<string, string>>  $existing
     * @return array<int, array{word:string,translation:string,transcription:string,phrases:string}>
     */
    private function mergeWithDefaultSampleWords(array $existing): array
    {
        $merged = [];
        $seen = [];

        foreach (array_merge($existing, $this->defaultSampleWords()) as $entry) {
            $word = trim((string) ($entry['word'] ?? ''));
            $translation = trim((string) ($entry['translation'] ?? ''));
            $transcription = trim((string) ($entry['transcription'] ?? ''));
            $phrases = trim((string) ($entry['phrases'] ?? ''));

            if ($word === '' || $translation === '' || $transcription === '') {
                continue;
            }

            $key = strtolower($word.'|'.$translation);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $merged[] = [
                'word' => $word,
                'translation' => $translation,
                'transcription' => $transcription,
                'phrases' => $phrases,
            ];
        }

        return $merged;
    }
}
