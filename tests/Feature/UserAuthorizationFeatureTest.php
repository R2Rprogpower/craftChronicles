<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Users\Enums\Permission as UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserAuthorizationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_or_view_users(): void
    {
        $user = User::factory()->create();

        $this->getJson('/api/users')->assertUnauthorized();
        $this->getJson("/api/users/{$user->id}")->assertUnauthorized();
    }

    public function test_authenticated_user_without_view_permission_cannot_list_or_view_users(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/users')
            ->assertForbidden()
            ->assertJsonMissing(['email' => $target->email]);

        $this->getJson("/api/users/{$target->id}")
            ->assertForbidden()
            ->assertJsonMissing(['email' => $target->email]);
    }

    public function test_user_with_view_permission_can_list_and_view_users(): void
    {
        $user = $this->createUserWithViewPermission();
        $target = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/users')
            ->assertOk()
            ->assertJsonFragment(['email' => $target->email]);

        $this->getJson("/api/users/{$target->id}")
            ->assertOk()
            ->assertJsonPath('data.email', $target->email);
    }

    public function test_user_with_view_permission_gets_not_found_for_missing_user(): void
    {
        $user = $this->createUserWithViewPermission();
        Sanctum::actingAs($user);

        $this->getJson('/api/users/999999')->assertNotFound();
    }

    private function createUserWithViewPermission(): User
    {
        $permission = Permission::query()->create([
            'name' => UserPermission::VIEW_USERS->value,
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        return $user;
    }
}
