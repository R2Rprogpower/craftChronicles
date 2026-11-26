<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebRegistrationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_registration_page(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Create account and continue');
    }

    public function test_guest_can_register_and_is_redirected_to_family_chronicles(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ruslan',
            'email' => 'ruslan@example.test',
            'password' => 'long-test-password',
            'password_confirmation' => 'long-test-password',
        ]);

        $user = User::query()->where('email', 'ruslan@example.test')->firstOrFail();

        $response->assertRedirect('/family-chronicles');
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_duplicate_email_and_short_password(): void
    {
        User::factory()->create(['email' => 'used@example.test']);

        $this->from('/register')->post('/register', [
            'name' => 'Ruslan',
            'email' => 'used@example.test',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertRedirect('/register')
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }
}
