<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BrandProfile;
use App\Models\ServiceOffering;
use App\Models\ServiceRequest;
use App\Modules\BrandProgress\Database\Seeders\BrandProgressSeeder;
use App\Modules\Content\Database\Seeders\ContentSeeder;
use App\Modules\PersonalBrand\Database\Seeders\PersonalBrandSeeder;
use App\Modules\Portfolio\Database\Seeders\PortfolioSeeder;
use App\Modules\Products\Database\Seeders\ProductsSeeder;
use App\Modules\Services\Database\Seeders\ServicesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandPlatformFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([PersonalBrandSeeder::class, PortfolioSeeder::class, ServicesSeeder::class, ProductsSeeder::class, ContentSeeder::class, BrandProgressSeeder::class]);
    }

    public function test_portfolio_pages_are_public_and_receive_configured_data(): void
    {
        $this->get('/portfolio')->assertOk()->assertSee('brand-platform')->assertSee(BrandProfile::query()->firstOrFail()->headline, false);
    }

    public function test_progress_dashboard_is_public(): void
    {
        $this->get('/progress')
            ->assertOk()
            ->assertViewHas('page', 'progress')
            ->assertViewHas('payload', fn (array $payload): bool => count($payload['areas']) >= 6);
    }

    public function test_service_request_is_validated_and_persisted(): void
    {
        $service = ServiceOffering::query()->firstOrFail();
        $response = $this->postJson('/service-requests', [
            'name' => 'Potential Client',
            'email' => 'client@example.com',
            'service_id' => $service->id,
            'message' => 'We need help modernizing an existing Laravel product.',
        ]);

        $response->assertCreated()->assertJsonPath('data.status', 'new');
        $this->assertDatabaseHas(ServiceRequest::class, ['email' => 'client@example.com', 'service_offering_id' => $service->id]);
    }

    public function test_service_request_requires_at_least_one_contact_method(): void
    {
        $this->postJson('/service-requests', ['name' => 'No Contact', 'message' => 'This message is long enough to be valid.'])
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'contact']);
    }
}
