<?php

namespace Tests\Feature;

use App\Models\App\Application;
use App\Models\App\Module;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private Cluster $cluster;
    private Application $application;
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::create(['name' => 'Thailand', 'code' => 'THA', 'code_alpha2' => 'TH', 'is_active' => true]);
        $this->cluster = Cluster::create([
            'name' => 'Pattaya', 'slug' => 'pattaya', 'code' => 'PTY',
            'country_id' => $country->id, 'is_active' => true, 'timezone' => 'Asia/Bangkok',
        ]);
        $this->application = Application::create([
            'name' => 'Hotel Mgmt', 'slug' => 'hotel-mgmt', 'code' => 'HOTEL',
            'type' => 'internal', 'is_active' => true,
        ]);
        $this->module = Module::create([
            'application_id' => $this->application->id, 'name' => 'Bookings',
            'slug' => 'bookings', 'code' => 'BOOKING', 'is_active' => true,
        ]);

        // Register a test route with module access middleware
        Route::middleware(['auth:sanctum', 'cluster.aware', 'module.access:HOTEL,BOOKING'])
            ->get('/test/protected', fn () => response()->json(['ok' => true]));
    }

    public function test_cluster_admin_passes_module_check(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Cluster Admin', 'slug' => 'cluster-admin', 'level' => 'cluster']);
        $user->roles()->attach($role->id, ['cluster_id' => $this->cluster->id]);

        $response = $this->actingAs($user)
            ->withHeaders(['X-Cluster-Id' => $this->cluster->id])
            ->getJson('/test/protected');

        $response->assertOk();
    }

    public function test_user_without_access_gets_403(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders(['X-Cluster-Id' => $this->cluster->id])
            ->getJson('/test/protected');

        $response->assertForbidden();
    }

    public function test_user_with_granted_access_passes(): void
    {
        $user = User::factory()->create();

        DB::table('user_app_access')->insert([
            'user_id' => $user->id, 'cluster_id' => $this->cluster->id,
            'application_id' => $this->application->id, 'has_access' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('user_module_access')->insert([
            'user_id' => $user->id, 'cluster_id' => $this->cluster->id,
            'module_id' => $this->module->id, 'has_access' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['X-Cluster-Id' => $this->cluster->id])
            ->getJson('/test/protected');

        $response->assertOk();
    }

    public function test_unauthenticated_user_gets_401(): void
    {
        $response = $this->getJson('/test/protected');

        $response->assertUnauthorized();
    }

    public function test_cluster_aware_middleware_rejects_invalid_cluster(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders(['X-Cluster-Id' => 9999])
            ->getJson('/api/clusters/1');

        $response->assertStatus(400);
    }
}
