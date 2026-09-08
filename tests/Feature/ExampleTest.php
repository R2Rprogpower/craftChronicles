<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_health_response_reports_ci_cd_deployment(): void
    {
        $response = $this->getJson('/api/health');

        $response
            ->assertOk()
            ->assertJsonPath('deployment_message', 'Это изменение было задеплоено через CI/CD');

        $this->assertStringContainsString(
            '"deployment_message":"Это изменение было задеплоено через CI/CD"',
            (string) $response->getContent(),
        );
    }
}
