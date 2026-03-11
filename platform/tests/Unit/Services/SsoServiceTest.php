<?php

namespace Tests\Unit\Services;

use App\Models\Auth\User;
use App\Services\Cluster\ClusterManager;
use App\Services\SSO\SsoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SsoServiceTest extends TestCase
{
    use RefreshDatabase;

    private SsoService $ssoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ssoService = new SsoService(app(ClusterManager::class));
    }

    public function test_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $result = $this->ssoService->authenticateWithCredentials('test@example.com', 'password123');

        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_authenticate_with_wrong_password_returns_null(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $result = $this->ssoService->authenticateWithCredentials('test@example.com', 'wrongpassword');

        $this->assertNull($result);
    }

    public function test_authenticate_with_nonexistent_email_returns_null(): void
    {
        $result = $this->ssoService->authenticateWithCredentials('noone@example.com', 'password123');

        $this->assertNull($result);
    }

    public function test_authenticate_with_inactive_user_returns_null(): void
    {
        User::factory()->create([
            'email' => 'suspended@example.com',
            'password' => Hash::make('password123'),
            'status' => 'suspended',
        ]);

        $result = $this->ssoService->authenticateWithCredentials('suspended@example.com', 'password123');

        $this->assertNull($result);
    }

    public function test_authenticate_with_provider_creates_new_user(): void
    {
        $result = $this->ssoService->authenticateWithProvider('google', 'google-123', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('john@example.com', $result->email);
        $this->assertEquals('google', $result->sso_provider);
        $this->assertEquals('google-123', $result->sso_provider_id);
    }

    public function test_authenticate_with_provider_returns_existing_user(): void
    {
        $user = User::factory()->create([
            'sso_provider' => 'google',
            'sso_provider_id' => 'google-123',
        ]);

        $result = $this->ssoService->authenticateWithProvider('google', 'google-123', [
            'name' => 'Different Name',
        ]);

        $this->assertEquals($user->id, $result->id);
    }

    public function test_authenticate_with_provider_links_existing_email(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'sso_provider' => null,
            'sso_provider_id' => null,
        ]);

        $result = $this->ssoService->authenticateWithProvider('facebook', 'fb-456', [
            'email' => 'existing@example.com',
            'name' => 'Existing User',
        ]);

        $this->assertEquals($user->id, $result->id);
        $this->assertEquals('facebook', $result->fresh()->sso_provider);
        $this->assertEquals('fb-456', $result->fresh()->sso_provider_id);
    }

    public function test_build_session_payload_structure(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'locale' => 'en',
        ]);

        $payload = $this->ssoService->buildSessionPayload($user);

        $this->assertArrayHasKey('user', $payload);
        $this->assertArrayHasKey('roles', $payload);
        $this->assertArrayHasKey('groups', $payload);
        $this->assertArrayHasKey('is_global_admin', $payload);
        $this->assertEquals('Test User', $payload['user']['name']);
        $this->assertEquals('test@example.com', $payload['user']['email']);
        $this->assertFalse($payload['is_global_admin']);
    }

    public function test_build_session_payload_with_cluster(): void
    {
        $user = User::factory()->create();
        $country = \App\Models\Global\Country::create([
            'name' => 'Thailand', 'code' => 'THA', 'code_alpha2' => 'TH', 'is_active' => true,
        ]);
        $cluster = \App\Models\Global\Cluster::create([
            'name' => 'Pattaya', 'slug' => 'pattaya', 'code' => 'PTY',
            'country_id' => $country->id, 'is_active' => true, 'timezone' => 'Asia/Bangkok',
        ]);

        $payload = $this->ssoService->buildSessionPayload($user, $cluster->id);

        $this->assertArrayHasKey('cluster', $payload);
        $this->assertEquals($cluster->id, $payload['cluster']['id']);
        $this->assertArrayHasKey('accessible_apps', $payload['cluster']);
        $this->assertArrayHasKey('accessible_modules', $payload['cluster']);
    }

    public function test_authenticate_records_last_login(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('pass'),
            'status' => 'active',
            'last_login_at' => null,
        ]);

        $this->ssoService->authenticateWithCredentials('login@example.com', 'pass');

        $this->assertNotNull($user->fresh()->last_login_at);
    }
}
