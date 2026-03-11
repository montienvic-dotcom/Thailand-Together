<?php

namespace Tests\Unit\Services;

use App\Models\App\Application;
use App\Models\App\Module;
use App\Models\Auth\Group;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use App\Services\Permission\PermissionResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class PermissionResolverTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    private PermissionResolver $resolver;
    private Country $country;
    private Cluster $cluster;
    private Application $application;
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new PermissionResolver();

        $this->country = $this->createCountry();
        $this->cluster = $this->createCluster($this->country);
        $this->application = Application::create([
            'name' => 'Hotel Mgmt', 'slug' => 'hotel-mgmt', 'code' => 'HOTEL',
            'type' => 'internal', 'is_active' => true,
        ]);
        $this->module = Module::create([
            'application_id' => $this->application->id,
            'name' => 'Bookings', 'slug' => 'bookings', 'code' => 'BOOKING', 'is_active' => true,
        ]);
    }

    public function test_global_admin_can_access_everything(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Global Admin', 'slug' => 'global-admin', 'level' => 'global', 'is_system' => true]);
        $user->roles()->attach($role->id);

        $this->assertTrue($this->resolver->canAccess($user, $this->cluster->id, $this->application->id, $this->module->id));
    }

    public function test_global_admin_accessible_clusters_returns_all(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Global Admin', 'slug' => 'global-admin', 'level' => 'global', 'is_system' => true]);
        $user->roles()->attach($role->id);

        $cluster2 = $this->createCluster($this->country, [
            'name' => 'Chiang Mai', 'slug' => 'chiangmai', 'code' => 'CNX',
        ]);

        $clusters = $this->resolver->accessibleClusters($user);
        $this->assertContains($this->cluster->id, $clusters);
        $this->assertContains($cluster2->id, $clusters);
    }

    public function test_country_admin_can_access_clusters_in_country(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Country Admin', 'slug' => 'country-admin', 'level' => 'country']);
        $user->roles()->attach($role->id, ['country_id' => $this->country->id]);

        $this->assertTrue($this->resolver->canAccess($user, $this->cluster->id, $this->application->id));
    }

    public function test_cluster_admin_can_access_own_cluster(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Cluster Admin', 'slug' => 'cluster-admin', 'level' => 'cluster']);
        $user->roles()->attach($role->id, ['cluster_id' => $this->cluster->id]);

        $this->assertTrue($this->resolver->canAccess($user, $this->cluster->id, $this->application->id, $this->module->id));
    }

    public function test_cluster_admin_cannot_access_other_cluster(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Cluster Admin', 'slug' => 'cluster-admin', 'level' => 'cluster']);
        $user->roles()->attach($role->id, ['cluster_id' => $this->cluster->id]);

        $otherCluster = $this->createCluster($this->country, [
            'name' => 'Chiang Mai', 'slug' => 'chiangmai', 'code' => 'CNX',
        ]);

        $this->assertFalse($this->resolver->canAccess($user, $otherCluster->id, $this->application->id));
    }

    public function test_user_with_app_access_can_access_app(): void
    {
        $user = User::factory()->create();

        DB::table('user_app_access')->insert([
            'user_id' => $user->id, 'cluster_id' => $this->cluster->id,
            'application_id' => $this->application->id, 'has_access' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->assertTrue($this->resolver->canAccess($user, $this->cluster->id, $this->application->id));
    }

    public function test_user_without_access_is_denied(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($this->resolver->canAccess($user, $this->cluster->id, $this->application->id));
    }

    public function test_user_with_denied_app_access(): void
    {
        $user = User::factory()->create();

        DB::table('user_app_access')->insert([
            'user_id' => $user->id, 'cluster_id' => $this->cluster->id,
            'application_id' => $this->application->id, 'has_access' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->assertFalse($this->resolver->canAccess($user, $this->cluster->id, $this->application->id));
    }

    public function test_user_with_module_access(): void
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

        $this->assertTrue($this->resolver->canAccess($user, $this->cluster->id, $this->application->id, $this->module->id));
    }

    public function test_group_access_grants_app_access(): void
    {
        $user = User::factory()->create();
        $group = Group::create([
            'name' => 'Operators', 'slug' => 'operators', 'scope' => 'global', 'is_active' => true,
        ]);
        $user->groups()->attach($group->id);

        DB::table('group_app_access')->insert([
            'group_id' => $group->id, 'cluster_id' => $this->cluster->id,
            'application_id' => $this->application->id, 'has_access' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->assertTrue($this->resolver->canAccess($user, $this->cluster->id, $this->application->id));
    }

    public function test_group_access_grants_module_access(): void
    {
        $user = User::factory()->create();
        $group = Group::create([
            'name' => 'Operators', 'slug' => 'operators', 'scope' => 'global', 'is_active' => true,
        ]);
        $user->groups()->attach($group->id);

        DB::table('group_app_access')->insert([
            'group_id' => $group->id, 'cluster_id' => $this->cluster->id,
            'application_id' => $this->application->id, 'has_access' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('group_module_access')->insert([
            'group_id' => $group->id, 'cluster_id' => $this->cluster->id,
            'module_id' => $this->module->id, 'has_access' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->assertTrue($this->resolver->canAccess($user, $this->cluster->id, $this->application->id, $this->module->id));
    }

    public function test_get_access_map_for_user(): void
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

        $map = $this->resolver->getAccessMap($user, $this->cluster->id);

        $this->assertArrayHasKey($this->application->id, $map);
        $this->assertContains($this->module->id, $map[$this->application->id]);
    }

    public function test_clear_cache_does_not_throw(): void
    {
        $user = User::factory()->create();
        $this->resolver->clearCache($user);
        $this->assertTrue(true);
    }

    public function test_accessible_clusters_from_role(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Cluster Admin', 'slug' => 'cluster-admin', 'level' => 'cluster']);
        $user->roles()->attach($role->id, ['cluster_id' => $this->cluster->id]);

        $clusters = $this->resolver->accessibleClusters($user);
        $this->assertContains($this->cluster->id, $clusters);
    }

    public function test_accessible_clusters_from_app_access(): void
    {
        $user = User::factory()->create();

        DB::table('user_app_access')->insert([
            'user_id' => $user->id, 'cluster_id' => $this->cluster->id,
            'application_id' => $this->application->id, 'has_access' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $clusters = $this->resolver->accessibleClusters($user);
        $this->assertContains($this->cluster->id, $clusters);
    }
}
