<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create base data for cluster-aware middleware
        $country = Country::create(['name' => 'Thailand', 'code' => 'THA', 'code_alpha2' => 'TH', 'is_active' => true]);
        Cluster::create([
            'name' => 'Pattaya', 'slug' => 'pattaya', 'code' => 'PTY',
            'country_id' => $country->id, 'is_active' => true, 'timezone' => 'Asia/Bangkok',
        ]);
    }

    public function test_login_with_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('secret123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@test.com',
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'session' => ['user', 'roles', 'groups', 'is_global_admin'],
            ]);
    }

    public function test_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('secret123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertUnauthorized()
            ->assertJson(['error' => 'Invalid credentials']);
    }

    public function test_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_sso_login_creates_new_user(): void
    {
        $response = $this->postJson('/api/auth/sso', [
            'provider' => 'google',
            'provider_id' => 'google-12345',
            'name' => 'Google User',
            'email' => 'google@test.com',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'session']);

        $this->assertDatabaseHas('users', [
            'email' => 'google@test.com',
            'sso_provider' => 'google',
        ]);
    }

    public function test_sso_login_validates_provider(): void
    {
        $response = $this->postJson('/api/auth/sso', [
            'provider' => 'invalid_provider',
            'provider_id' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['provider']);
    }

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJson(['message' => 'Logged out']);

        // Token should be revoked
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_session_returns_user_info(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/auth/session');

        $response->assertOk()
            ->assertJsonStructure([
                'session' => ['user', 'roles', 'groups', 'is_global_admin'],
            ]);
    }

    public function test_protected_route_requires_auth(): void
    {
        $response = $this->getJson('/api/auth/session');

        $response->assertUnauthorized();
    }
}
