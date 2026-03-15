<?php

namespace Tests\Feature;

use App\Models\Auth\Group;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Country $country;
    protected Cluster $cluster;

    protected function setUp(): void
    {
        parent::setUp();

        // Create base data for cluster-aware middleware
        $this->country = Country::create([
            'name' => 'Thailand',
            'code' => 'THA',
            'code_alpha2' => 'TH',
            'is_active' => true,
        ]);

        $this->cluster = Cluster::create([
            'name' => 'Pattaya',
            'slug' => 'pattaya',
            'code' => 'PTY',
            'country_id' => $this->country->id,
            'is_active' => true,
            'timezone' => 'Asia/Bangkok',
        ]);

        $this->user = User::factory()->create();
    }

    // ─── Group Tests ──────────────────────────────────────────────

    public function test_store_group(): void
    {
        $response = $this->actingAs($this->user)->postJson('/admin/permissions/groups', [
            'name' => 'Operators',
            'description' => 'Operator group for Pattaya',
            'scope' => 'cluster',
            'country_id' => $this->country->id,
            'cluster_id' => $this->cluster->id,
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Group created successfully.',
            ])
            ->assertJsonPath('group.name', 'Operators')
            ->assertJsonPath('group.slug', 'operators')
            ->assertJsonPath('group.scope', 'cluster');

        $this->assertDatabaseHas('groups', [
            'name' => 'Operators',
            'slug' => 'operators',
            'scope' => 'cluster',
            'country_id' => $this->country->id,
            'cluster_id' => $this->cluster->id,
        ]);
    }

    public function test_store_group_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/admin/permissions/groups', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'scope']);
    }

    public function test_update_group(): void
    {
        $group = Group::create([
            'name' => 'Old Name',
            'slug' => 'old-name',
            'description' => 'Old description',
            'scope' => 'global',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->user)->putJson("/admin/permissions/groups/{$group->id}", [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'scope' => 'country',
            'country_id' => $this->country->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Group updated successfully.',
            ])
            ->assertJsonPath('group.name', 'Updated Name')
            ->assertJsonPath('group.slug', 'updated-name')
            ->assertJsonPath('group.scope', 'country');
    }

    public function test_toggle_group(): void
    {
        $group = Group::create([
            'name' => 'Toggle Group',
            'slug' => 'toggle-group',
            'scope' => 'global',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->user)->patchJson("/admin/permissions/groups/{$group->id}/toggle");

        $response->assertOk()
            ->assertJson([
                'message' => 'Group deactivated.',
                'is_active' => false,
            ]);

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'is_active' => false,
        ]);
    }

    public function test_delete_group(): void
    {
        $group = Group::create([
            'name' => 'Delete Me',
            'slug' => 'delete-me',
            'scope' => 'global',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/admin/permissions/groups/{$group->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Group deleted successfully.']);

        $this->assertSoftDeleted('groups', ['id' => $group->id]);
    }

    // ─── Role Tests ───────────────────────────────────────────────

    public function test_store_role(): void
    {
        $response = $this->actingAs($this->user)->postJson('/admin/permissions/roles', [
            'name' => 'Cluster Admin',
            'description' => 'Administrator for a cluster',
            'level' => 'cluster',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Role created successfully.',
            ])
            ->assertJsonPath('role.name', 'Cluster Admin')
            ->assertJsonPath('role.slug', 'cluster-admin')
            ->assertJsonPath('role.level', 'cluster')
            ->assertJsonPath('role.is_system', false);

        $this->assertDatabaseHas('roles', [
            'name' => 'Cluster Admin',
            'slug' => 'cluster-admin',
            'level' => 'cluster',
        ]);
    }

    public function test_update_role(): void
    {
        $role = Role::create([
            'name' => 'Old Role',
            'slug' => 'old-role',
            'description' => 'Old description',
            'level' => 'global',
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->user)->putJson("/admin/permissions/roles/{$role->id}", [
            'name' => 'Updated Role',
            'description' => 'Updated description',
            'level' => 'country',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Role updated successfully.',
            ])
            ->assertJsonPath('role.name', 'Updated Role')
            ->assertJsonPath('role.slug', 'updated-role')
            ->assertJsonPath('role.level', 'country');
    }

    public function test_delete_role(): void
    {
        $role = Role::create([
            'name' => 'Deletable Role',
            'slug' => 'deletable-role',
            'level' => 'app',
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/admin/permissions/roles/{$role->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Role deleted successfully.']);

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_delete_system_role_fails(): void
    {
        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'level' => 'global',
            'is_system' => true,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/admin/permissions/roles/{$role->id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'System roles cannot be deleted.']);

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    public function test_get_role_permissions(): void
    {
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
            'level' => 'cluster',
            'is_system' => false,
        ]);

        $permA = Permission::create([
            'name' => 'View Dashboard',
            'slug' => 'view-dashboard',
            'description' => 'Can view dashboard',
            'category' => 'dashboard',
        ]);

        $permB = Permission::create([
            'name' => 'Manage Users',
            'slug' => 'manage-users',
            'description' => 'Can manage users',
            'category' => 'users',
        ]);

        $role->permissions()->attach([$permA->id, $permB->id]);

        $response = $this->actingAs($this->user)->getJson("/admin/permissions/roles/{$role->id}/permissions");

        $response->assertOk()
            ->assertJsonStructure(['permission_ids'])
            ->assertJsonCount(2, 'permission_ids');

        $ids = $response->json('permission_ids');
        $this->assertContains($permA->id, $ids);
        $this->assertContains($permB->id, $ids);
    }

    public function test_sync_role_permissions(): void
    {
        $role = Role::create([
            'name' => 'Sync Role',
            'slug' => 'sync-role',
            'level' => 'cluster',
            'is_system' => false,
        ]);

        $permA = Permission::create([
            'name' => 'Permission A',
            'slug' => 'permission-a',
            'description' => 'First permission',
            'category' => 'general',
        ]);

        $permB = Permission::create([
            'name' => 'Permission B',
            'slug' => 'permission-b',
            'description' => 'Second permission',
            'category' => 'general',
        ]);

        $permC = Permission::create([
            'name' => 'Permission C',
            'slug' => 'permission-c',
            'description' => 'Third permission',
            'category' => 'general',
        ]);

        // Attach initial permissions
        $role->permissions()->attach([$permA->id]);

        // Sync to a new set
        $response = $this->actingAs($this->user)->putJson("/admin/permissions/roles/{$role->id}/sync-permissions", [
            'permission_ids' => [$permB->id, $permC->id],
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Role permissions updated successfully.',
            ]);

        // Verify the sync replaced old permissions
        $currentIds = $role->fresh()->permissions()->pluck('permissions.id')->toArray();
        $this->assertCount(2, $currentIds);
        $this->assertContains($permB->id, $currentIds);
        $this->assertContains($permC->id, $currentIds);
        $this->assertNotContains($permA->id, $currentIds);
    }

    // ─── Auth Guard Test ──────────────────────────────────────────

    public function test_unauthenticated_cannot_manage_permissions(): void
    {
        $response = $this->postJson('/admin/permissions/groups', [
            'name' => 'Test',
            'scope' => 'global',
        ]);
        $response->assertUnauthorized();

        $response = $this->postJson('/admin/permissions/roles', [
            'name' => 'Test',
            'level' => 'global',
        ]);
        $response->assertUnauthorized();

        $response = $this->getJson('/admin/permissions/roles/1/permissions');
        $response->assertUnauthorized();
    }
}
