<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Modules\FamilyChronicles\Models\BiographyAnswer;
use App\Modules\FamilyChronicles\Models\FamilyPerson;
use App\Modules\FamilyChronicles\Services\QuestionBank;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FamilyChroniclesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/family-chronicles')->assertRedirect('/login');
        $this->getJson('/family-chronicles/state')->assertUnauthorized();
    }

    public function test_authenticated_user_can_open_accessible_single_page_interface(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/family-chronicles')
            ->assertOk()
            ->assertSee('Семейная летопись')
            ->assertSee('Вопрос дня')
            ->assertSee('family-chronicles.js');
    }

    public function test_user_can_create_a_person_and_receive_stable_daily_question(): void
    {
        $user = User::factory()->create();

        $personResponse = $this->actingAs($user)->postJson('/family-chronicles/people', [
            'name' => 'Анна',
            'birth_year' => 1952,
            'relationship' => 'мама',
        ]);

        $personResponse->assertCreated()->assertJsonPath('person.name', 'Анна');
        $personId = (int) $personResponse->json('person.id');

        $first = $this->actingAs($user)->getJson("/family-chronicles/state?person_id={$personId}")
            ->assertOk()
            ->assertJsonPath('active_person_id', $personId)
            ->assertJsonCount(count((new QuestionBank)->availableFor(1952)), 'questions');

        $dailyKey = (string) $first->json('daily_question_key');
        $this->assertNotSame('', $dailyKey);

        $this->actingAs($user)->postJson('/family-chronicles/answers', [
            'person_id' => $personId,
            'question_key' => $dailyKey,
            'status' => 'answered',
            'text' => 'Подробное семейное воспоминание.',
        ])->assertOk()->assertJsonPath('answer.status', 'answered');

        $this->actingAs($user)->getJson("/family-chronicles/state?person_id={$personId}")
            ->assertOk()
            ->assertJsonPath('daily_question_key', $dailyKey)
            ->assertJsonPath("answers.{$dailyKey}.text", 'Подробное семейное воспоминание.')
            ->assertJsonPath('progress.answered', 1);
    }

    public function test_users_cannot_read_or_change_another_users_family_data(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $person = FamilyPerson::query()->create([
            'user_id' => $owner->id,
            'name' => 'Закрытый профиль',
            'birth_year' => 1960,
            'locale' => 'ru',
        ]);
        $questionKey = (new QuestionBank)->all()[0]['key'];
        $answer = BiographyAnswer::query()->create([
            'user_id' => $owner->id,
            'family_person_id' => $person->id,
            'question_key' => $questionKey,
            'status' => 'answered',
            'text' => 'Приватный ответ',
        ]);

        $this->actingAs($outsider)
            ->getJson("/family-chronicles/state?person_id={$person->id}")
            ->assertNotFound();

        $this->actingAs($outsider)->postJson('/family-chronicles/answers', [
            'person_id' => $person->id,
            'question_key' => $questionKey,
            'status' => 'answered',
            'text' => 'Попытка подмены',
        ])->assertNotFound();

        $this->actingAs($outsider)
            ->postJson("/family-chronicles/answers/{$answer->id}/attachments", [
                'file' => UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg'),
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('biography_answers', [
            'id' => $answer->id,
            'text' => 'Приватный ответ',
        ]);
    }

    public function test_owner_can_upload_and_delete_private_attachment(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $person = FamilyPerson::query()->create([
            'user_id' => $user->id,
            'name' => 'Пётр',
            'locale' => 'ru',
        ]);
        $answer = BiographyAnswer::query()->create([
            'user_id' => $user->id,
            'family_person_id' => $person->id,
            'question_key' => (new QuestionBank)->all()[0]['key'],
            'status' => 'draft',
            'text' => 'Черновик',
        ]);

        $response = $this->actingAs($user)
            ->postJson("/family-chronicles/answers/{$answer->id}/attachments", [
                'file' => UploadedFile::fake()->create('family-photo.jpg', 10, 'image/jpeg'),
            ])
            ->assertCreated()
            ->assertJsonPath('attachment.name', 'family-photo.jpg');

        $attachmentId = (int) $response->json('attachment.id');
        $path = (string) $this->app['db']->table('biography_attachments')->where('id', $attachmentId)->value('path');
        Storage::disk('local')->assertExists($path);

        $this->actingAs($user)
            ->deleteJson("/family-chronicles/attachments/{$attachmentId}")
            ->assertOk();

        Storage::disk('local')->assertMissing($path);
        $this->assertDatabaseMissing('biography_attachments', ['id' => $attachmentId]);
    }

    public function test_question_bank_is_large_and_avoids_future_life_stages(): void
    {
        $bank = new QuestionBank;
        $this->assertGreaterThanOrEqual(150, count($bank->all()));

        $questionsForTeenager = $bank->availableFor((int) now()->format('Y') - 14);
        $this->assertNotEmpty($questionsForTeenager);
        $this->assertFalse(collect($questionsForTeenager)->contains('stage', 'age-19-25'));
        $this->assertTrue(collect($questionsForTeenager)->contains('stage', 'age-11-14'));
    }

    public function test_family_traditions_have_at_least_fifty_unique_questions(): void
    {
        $familyQuestions = collect((new QuestionBank)->all())
            ->where('category', 'family');

        $this->assertGreaterThanOrEqual(50, $familyQuestions->count());
        $this->assertSame($familyQuestions->count(), $familyQuestions->pluck('key')->unique()->count());
        $this->assertSame($familyQuestions->count(), $familyQuestions->pluck('text')->unique()->count());
    }

    public function test_expanded_life_themes_have_large_unique_question_sets(): void
    {
        $questions = collect((new QuestionBank)->all());

        foreach ([
            'daily-life' => 100,
            'relationships' => 80,
            'turning-points' => 90,
        ] as $category => $minimum) {
            $categoryQuestions = $questions->where('category', $category);

            $this->assertGreaterThanOrEqual($minimum, $categoryQuestions->count(), $category);
            $this->assertSame($categoryQuestions->count(), $categoryQuestions->pluck('key')->unique()->count(), $category);
            $this->assertSame($categoryQuestions->count(), $categoryQuestions->pluck('text')->unique()->count(), $category);
        }

        $this->assertSame($questions->count(), $questions->pluck('key')->unique()->count());
        $this->assertSame($questions->count(), $questions->pluck('text')->unique()->count());
    }

    public function test_profile_relatives_personalize_and_filter_questions_only_when_complete(): void
    {
        $user = User::factory()->create();
        $person = FamilyPerson::query()->create([
            'user_id' => $user->id,
            'name' => 'Людмила',
            'birth_date' => '1960-05-12',
            'birth_year' => 1960,
            'locale' => 'ru',
            'relatives_complete' => false,
        ]);

        $this->actingAs($user)->postJson("/family-chronicles/people/{$person->id}/relatives", [
            'relationship' => 'mother',
            'name' => 'Тамара',
        ])->assertCreated();

        $incompleteQuestions = collect($this->actingAs($user)
            ->getJson("/family-chronicles/state?person_id={$person->id}")
            ->assertOk()
            ->json('questions'));

        $this->assertTrue($incompleteQuestions->contains('key', 'relative-brother-game'));
        $this->assertStringContainsString(
            'мама по имени Тамара',
            (string) $incompleteQuestions->firstWhere('key', 'relative-mother-recipe')['text'],
        );

        $this->actingAs($user)->patchJson("/family-chronicles/people/{$person->id}", [
            'name' => 'Людмила',
            'birth_date' => '1960-05-12',
            'relationship' => 'мама',
            'gender' => 'female',
            'birth_place' => 'Харьков',
            'residence' => 'Киев',
            'occupation' => 'Инженер',
            'profile_notes' => 'Любит семейные фотографии.',
            'relatives_complete' => true,
        ])->assertOk()->assertJsonPath('person.relatives_complete', true);

        $completeResponse = $this->actingAs($user)
            ->getJson("/family-chronicles/state?person_id={$person->id}")
            ->assertOk()
            ->assertJsonPath('people.0.birth_place', 'Харьков')
            ->assertJsonPath('people.0.relatives.0.name', 'Тамара');
        $completeQuestions = collect($completeResponse->json('questions'));

        $this->assertFalse($completeQuestions->contains('key', 'relative-brother-game'));
        $this->assertTrue($completeQuestions->contains('key', 'relative-mother-recipe'));
        $this->assertStringContainsString(
            'Людмила',
            (string) $completeQuestions->firstWhere('key', 'relative-person-tradition')['text'],
        );
    }
}
