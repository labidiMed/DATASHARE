<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'jean@example.com',
            'password' => 'motdepasse123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user' => ['id', 'email']]);

        $this->assertDatabaseHas('users', ['email' => 'jean@example.com']);
    }

    public function test_register_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'jean@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'email' => 'jean@example.com',
            'password' => 'motdepasse123',
        ])->assertStatus(422);
    }

    public function test_password_must_have_at_least_8_characters(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'email' => 'court@example.com',
            'password' => 'court',
        ])->assertStatus(422);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'jean@example.com',
            'password' => 'motdepasse123',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'jean@example.com',
            'password' => 'motdepasse123',
        ])->assertOk()->assertJsonStructure(['access_token']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'jean@example.com',
            'password' => 'motdepasse123',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'jean@example.com',
            'password' => 'mauvais',
        ])->assertStatus(401);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }
}
