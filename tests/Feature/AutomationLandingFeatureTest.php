<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ServiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomationLandingFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_automation_landing_is_public_and_rendered_from_json_content(): void
    {
        $this->get('/ai-automation')
            ->assertOk()
            ->assertSee('Настраиваю OpenClaw так, чтобы он влиял на прибыль')
            ->assertSee('Менять сайт через Telegram без очереди к разработчику')
            ->assertSee('Production и CI/CD')
            ->assertSee('OpenClaw Business System')
            ->assertSee('Какой бизнес-результат важнее всего')
            ->assertSee(route('automation-landing.request'), false);
    }

    public function test_automation_inquiry_is_validated_and_persisted_with_metadata(): void
    {
        $response = $this->postJson('/ai-automation/request', [
            'name' => 'Ирина',
            'company' => 'Service Lab',
            'email' => 'irina@example.com',
            'business_type' => 'Сервисная компания',
            'company_size' => '12 человек',
            'current_stack' => 'Telegram, Google Drive и amoCRM',
            'budget' => '$3k-$7k',
            'interests' => ['more-qualified-leads', 'management-visibility', 'ai-cost-control'],
            'message' => 'Хотим быстрее обрабатывать входящие заявки и видеть причины потерь.',
            'consent' => '1',
            'website' => '',
        ]);

        $response->assertCreated()->assertJsonPath('data.status', 'new');

        $request = ServiceRequest::query()->sole();
        $this->assertSame('automation-landing', $request->source);
        $this->assertSame('Service Lab', $request->company);
        $this->assertSame(['more-qualified-leads', 'management-visibility', 'ai-cost-control'], $request->metadata['interests']);
        $this->assertSame('Telegram, Google Drive и amoCRM', $request->metadata['current_stack']);
    }

    public function test_automation_inquiry_rejects_unknown_interests_and_spam_honeypot(): void
    {
        $this->postJson('/ai-automation/request', [
            'name' => 'Robot',
            'email' => 'robot@example.com',
            'interests' => ['unknown-service'],
            'message' => 'This is a long enough automated spam message for validation.',
            'consent' => '1',
            'website' => 'spam.example',
        ])->assertUnprocessable()->assertJsonValidationErrors(['interests.0', 'website']);

        $this->assertDatabaseCount('service_requests', 0);
    }
}
