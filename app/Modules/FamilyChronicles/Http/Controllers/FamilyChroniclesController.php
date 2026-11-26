<?php

declare(strict_types=1);

namespace App\Modules\FamilyChronicles\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FamilyChronicles\Models\BiographyAnswer;
use App\Modules\FamilyChronicles\Models\BiographyAttachment;
use App\Modules\FamilyChronicles\Models\BiographyDailyQuestion;
use App\Modules\FamilyChronicles\Models\FamilyPerson;
use App\Modules\FamilyChronicles\Models\FamilyRelative;
use App\Modules\FamilyChronicles\Services\QuestionBank;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FamilyChroniclesController extends Controller
{
    /** @var array<string, string> */
    private const RELATIONSHIP_TYPES = [
        'mother' => 'Мама',
        'father' => 'Папа',
        'brother' => 'Брат',
        'sister' => 'Сестра',
        'spouse' => 'Супруг или супруга',
        'son' => 'Сын',
        'daughter' => 'Дочь',
        'grandmother' => 'Бабушка',
        'grandfather' => 'Дедушка',
        'other' => 'Другой родственник',
    ];

    public function index(QuestionBank $questions): View
    {
        return view('family-chronicles.index', [
            'questionCount' => count($questions->all()),
        ]);
    }

    public function state(Request $request, QuestionBank $bank): JsonResponse
    {
        $request->validate([
            'person_id' => ['nullable', 'integer'],
        ]);

        /** @var Collection<int, FamilyPerson> $people */
        $people = FamilyPerson::query()
            ->with('relatives')
            ->where('user_id', $request->user()->id)
            ->orderBy('name')
            ->get();

        /** @var FamilyPerson|null $person */
        $person = null;
        if ($request->filled('person_id')) {
            $person = $people->firstWhere('id', $request->integer('person_id'));
            abort_if($person === null, 404);
        } elseif ($people->isNotEmpty()) {
            $person = $people->first();
        }

        if ($person === null) {
            return response()->json([
                'people' => $people,
                'active_person_id' => null,
                'questions' => [],
                'answers' => [],
                'daily_question_key' => null,
                'categories' => $bank->categories(),
                'stages' => $this->stageLabels($bank),
                'relationship_types' => self::RELATIONSHIP_TYPES,
                'progress' => ['answered' => 0, 'total' => 0, 'percent' => 0],
            ]);
        }

        $rawBirthDate = $person->getRawOriginal('birth_date');
        $birthYear = is_string($rawBirthDate) && $rawBirthDate !== ''
            ? (int) substr($rawBirthDate, 0, 4)
            : $person->birth_year;
        $questions = $bank->availableForProfile(
            $birthYear,
            $person->relatives->map(static fn (FamilyRelative $relative): array => [
                'relationship' => $relative->relationship,
                'name' => $relative->name,
            ])->values()->all(),
            $person->relatives_complete,
            $person->name,
        );
        $questionKeys = array_column($questions, 'key');
        /** @var Collection<int, BiographyAnswer> $answers */
        $answers = BiographyAnswer::query()
            ->with('attachments')
            ->where('user_id', $request->user()->id)
            ->where('family_person_id', $person->id)
            ->whereIn('question_key', $questionKeys)
            ->get();

        $answeredKeys = $answers->where('status', 'answered')->pluck('question_key')->all();
        $daily = $this->dailyQuestion($request, $person, $questions, $answeredKeys);
        $answered = count($answeredKeys);
        $total = count($questions);

        return response()->json([
            'people' => $people,
            'active_person_id' => $person->id,
            'questions' => $questions,
            'answers' => $answers->mapWithKeys(fn (BiographyAnswer $answer): array => [
                $answer->question_key => $this->answerPayload($answer),
            ]),
            'daily_question_key' => $daily?->question_key,
            'categories' => $bank->categories(),
            'stages' => $this->stageLabels($bank),
            'relationship_types' => self::RELATIONSHIP_TYPES,
            'progress' => [
                'answered' => $answered,
                'total' => $total,
                'percent' => $total === 0 ? 0 : (int) round(($answered / $total) * 100),
            ],
        ]);
    }

    public function storePerson(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'birth_year' => ['nullable', 'integer', 'min:1900', 'max:'.now()->year],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'relationship' => ['nullable', 'string', 'max:80'],
            'gender' => ['nullable', Rule::in(['female', 'male', 'other', 'unspecified'])],
            'birth_place' => ['nullable', 'string', 'max:160'],
            'residence' => ['nullable', 'string', 'max:160'],
            'occupation' => ['nullable', 'string', 'max:160'],
            'profile_notes' => ['nullable', 'string', 'max:5000'],
            'relatives_complete' => ['nullable', 'boolean'],
            'locale' => ['nullable', Rule::in(['ru', 'uk', 'en'])],
        ]);

        if (! empty($validated['birth_date'])) {
            $validated['birth_year'] = (int) substr($validated['birth_date'], 0, 4);
        }

        $person = FamilyPerson::query()->create([
            ...$validated,
            'user_id' => $request->user()->id,
            'locale' => $validated['locale'] ?? 'ru',
            'relatives_complete' => $validated['relatives_complete'] ?? false,
        ]);

        $person->load('relatives');

        return response()->json(['person' => $person], 201);
    }

    public function updatePerson(Request $request, FamilyPerson $person): JsonResponse
    {
        abort_unless($person->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'relationship' => ['nullable', 'string', 'max:80'],
            'gender' => ['nullable', Rule::in(['female', 'male', 'other', 'unspecified'])],
            'birth_place' => ['nullable', 'string', 'max:160'],
            'residence' => ['nullable', 'string', 'max:160'],
            'occupation' => ['nullable', 'string', 'max:160'],
            'profile_notes' => ['nullable', 'string', 'max:5000'],
            'relatives_complete' => ['required', 'boolean'],
        ]);

        $validated['birth_year'] = ! empty($validated['birth_date'])
            ? (int) substr($validated['birth_date'], 0, 4)
            : $person->birth_year;

        $person->update($validated);
        $person->load('relatives');

        return response()->json(['person' => $person]);
    }

    public function storeRelative(Request $request, FamilyPerson $person): JsonResponse
    {
        abort_unless($person->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'relationship' => ['required', Rule::in(array_keys(self::RELATIONSHIP_TYPES))],
            'name' => ['nullable', 'string', 'max:120'],
            'birth_year' => ['nullable', 'integer', 'min:1850', 'max:'.now()->year],
        ]);

        $relative = FamilyRelative::query()->create([
            ...$validated,
            'user_id' => $request->user()->id,
            'family_person_id' => $person->id,
        ]);

        return response()->json(['relative' => $relative], 201);
    }

    public function destroyRelative(Request $request, FamilyRelative $relative): JsonResponse
    {
        abort_unless($relative->user_id === $request->user()->id, 404);
        $relative->delete();

        return response()->json(['message' => 'Родственник удалён']);
    }

    public function storeAnswer(Request $request, QuestionBank $bank): JsonResponse
    {
        $validated = $request->validate([
            'person_id' => ['required', 'integer'],
            'question_key' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::in(['draft', 'answered', 'skipped', 'deferred'])],
            'text' => ['nullable', 'string', 'max:100000'],
        ]);

        $person = $this->ownedPerson($request, (int) $validated['person_id']);
        if ($bank->find($validated['question_key']) === null) {
            throw ValidationException::withMessages(['question_key' => 'Неизвестный вопрос.']);
        }

        $answer = BiographyAnswer::query()->updateOrCreate(
            [
                'family_person_id' => $person->id,
                'question_key' => $validated['question_key'],
            ],
            [
                'user_id' => $request->user()->id,
                'status' => $validated['status'],
                'text' => $validated['text'] ?? null,
                'answered_at' => $validated['status'] === 'answered' ? now() : null,
            ],
        );

        $answer->load('attachments');

        return response()->json([
            'message' => $validated['status'] === 'draft' ? 'Черновик сохранён' : 'Ответ сохранён',
            'answer' => $this->answerPayload($answer),
        ]);
    }

    public function storeAttachment(Request $request, BiographyAnswer $answer): JsonResponse
    {
        $this->assertOwnedAnswer($request, $answer);
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'max:51200',
                'mimetypes:image/jpeg,image/png,image/webp,audio/mpeg,audio/wav,audio/ogg,audio/webm,video/mp4,video/webm,video/quicktime,application/pdf',
            ],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        $file = $validated['file'];
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $path = $file->storeAs(
            "family-chronicles/{$request->user()->id}/{$answer->family_person_id}",
            Str::uuid()->toString().".{$extension}",
            'local',
        );

        $attachment = BiographyAttachment::query()->create([
            'user_id' => $request->user()->id,
            'biography_answer_id' => $answer->id,
            'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'path' => $path,
            'mime_type' => (string) $file->getMimeType(),
            'size' => (int) $file->getSize(),
            'caption' => $validated['caption'] ?? null,
        ]);

        return response()->json(['attachment' => $this->attachmentPayload($attachment)], 201);
    }

    public function destroyAttachment(Request $request, BiographyAttachment $attachment): JsonResponse
    {
        abort_unless($attachment->user_id === $request->user()->id, 404);
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');
        $disk->delete($attachment->path);
        $attachment->delete();

        return response()->json(['message' => 'Файл удалён']);
    }

    public function downloadAttachment(Request $request, BiographyAttachment $attachment): StreamedResponse
    {
        abort_unless($attachment->user_id === $request->user()->id, 404);
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');
        abort_unless($disk->exists($attachment->path), 404);

        return $disk->download($attachment->path, $attachment->original_name, [
            'Content-Type' => $attachment->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function ownedPerson(Request $request, int $personId): FamilyPerson
    {
        return FamilyPerson::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($personId);
    }

    private function assertOwnedAnswer(Request $request, BiographyAnswer $answer): void
    {
        abort_unless($answer->user_id === $request->user()->id, 404);
    }

    /** @param list<array{key:string, category:string, category_label:string, stage:string, stage_label:string, text:string, requires:list<string>}> $questions
     * @param  list<string>  $answeredKeys
     */
    private function dailyQuestion(Request $request, FamilyPerson $person, array $questions, array $answeredKeys): ?BiographyDailyQuestion
    {
        if ($questions === []) {
            return null;
        }

        $date = now()->toDateString();
        /** @var BiographyDailyQuestion|null $existing */
        $existing = BiographyDailyQuestion::query()
            ->where('user_id', $request->user()->id)
            ->where('family_person_id', $person->id)
            ->whereDate('question_date', $date)
            ->first();

        if ($existing !== null && in_array($existing->question_key, array_column($questions, 'key'), true)) {
            return $existing;
        }

        $existing?->delete();

        $candidates = array_values(array_filter(
            $questions,
            static fn (array $question): bool => ! in_array($question['key'], $answeredKeys, true),
        ));
        if ($candidates === []) {
            $candidates = $questions;
        }

        $seed = sprintf('%s:%d:%d', $date, $request->user()->id, $person->id);
        $index = abs((int) crc32($seed)) % count($candidates);

        return BiographyDailyQuestion::query()->create([
            'user_id' => $request->user()->id,
            'family_person_id' => $person->id,
            'question_date' => $date,
            'question_key' => $candidates[$index]['key'],
        ]);
    }

    /** @return array<string, string> */
    private function stageLabels(QuestionBank $bank): array
    {
        return array_map(static fn (array $stage): string => $stage['label'], $bank->stages());
    }

    /** @return array<string, mixed> */
    private function answerPayload(BiographyAnswer $answer): array
    {
        return [
            'id' => $answer->id,
            'question_key' => $answer->question_key,
            'status' => $answer->status,
            'text' => $answer->text ?? '',
            'updated_at' => $answer->updated_at?->toIso8601String(),
            'attachments' => $answer->attachments->map(fn (BiographyAttachment $attachment): array => $this->attachmentPayload($attachment))->values(),
        ];
    }

    /** @return array<string, mixed> */
    private function attachmentPayload(BiographyAttachment $attachment): array
    {
        return [
            'id' => $attachment->id,
            'name' => $attachment->original_name,
            'mime_type' => $attachment->mime_type,
            'size' => $attachment->size,
            'caption' => $attachment->caption,
            'download_url' => route('family-chronicles.attachments.download', $attachment, false),
        ];
    }
}
