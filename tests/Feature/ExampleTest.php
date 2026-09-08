<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
        DB::shouldReceive('connection->getPdo')->once()->andReturn(new \stdClass);

        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('deployment_message', 'Это изменение было задеплоено через CI/CD');
    }
}
