<?php

namespace Tests\Feature;

use App\Models\App\Application;
use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminClusterTest extends TestCase
{
    use RefreshDatabase;

    protected Country $country;
    protected Cluster $cluster;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::create([
            'name' => 'Thailand',
            'code' => 'THA',
            'code_alpha2' => 'TH',
            'currency_code' => 'THB',
            'timezone' => 'Asia/Bangkok',
            'default_locale' => 'th',
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

    public function test_store_cluster_creates_new_cluster(): void
    {
        $response = $this->actingAs($this->user)->postJson('/admin/clusters', [
            'name' => 'Chiang Mai',
            'country_id' => $this->country->id,
            'code' => 'CNX',
            'description' => 'Northern Thailand cluster',
            'timezone' => 'Asia/Bangkok',
            'default_locale' => 'th',
            'database_connection' => 'mysql_cnx',
            'launch_date' => '2026-06-01',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Cluster created successfully.',
            ])
            ->assertJsonStructure([
                'message',
                'cluster' => ['id', 'name', 'slug', 'code', 'country_id', 'country'],
            ]);

        $this->assertDatabaseHas('clusters', [
            'name' => 'Chiang Mai',
            'code' => 'CNX',
            'slug' => 'chiang-mai',
        ]);
    }

    public function test_store_cluster_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/admin/clusters', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'country_id']);
    }

    public function test_update_cluster(): void
    {
        $response = $this->actingAs($this->user)->putJson("/admin/clusters/{$this->cluster->id}", [
            'name' => 'Pattaya Updated',
            'description' => 'Updated description',
            'timezone' => 'Asia/Bangkok',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Cluster updated successfully.',
            ])
            ->assertJsonStructure([
                'message',
                'cluster' => ['id', 'name', 'country'],
            ]);

        $this->assertDatabaseHas('clusters', [
            'id' => $this->cluster->id,
            'name' => 'Pattaya Updated',
            'description' => 'Updated description',
        ]);
    }

    public function test_toggle_cluster_active_status(): void
    {
        $this->assertTrue($this->cluster->is_active);

        $response = $this->actingAs($this->user)->patchJson("/admin/clusters/{$this->cluster->id}/toggle");

        $response->assertOk()
            ->assertJson([
                'message' => 'Cluster deactivated.',
                'is_active' => false,
            ]);

        $this->assertDatabaseHas('clusters', [
            'id' => $this->cluster->id,
            'is_active' => false,
        ]);
    }

    public function test_delete_cluster(): void
    {
        $response = $this->actingAs($this->user)->deleteJson("/admin/clusters/{$this->cluster->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Cluster deleted successfully.']);

        $this->assertDatabaseMissing('clusters', [
            'id' => $this->cluster->id,
        ]);
    }

    public function test_sync_applications_to_cluster(): void
    {
        $app1 = Application::create([
            'name' => 'App Together',
            'slug' => 'app-together',
            'code' => 'APP_TOGETHER',
            'type' => 'mobile',
            'is_active' => true,
        ]);

        $app2 = Application::create([
            'name' => 'Hotel Management',
            'slug' => 'hotel-management',
            'code' => 'HOTEL_MGMT',
            'type' => 'web',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->putJson("/admin/clusters/{$this->cluster->id}/sync-apps", [
            'application_ids' => [$app1->id, $app2->id],
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Applications updated for cluster.',
                'count' => 2,
            ]);

        $this->assertDatabaseHas('cluster_application', [
            'cluster_id' => $this->cluster->id,
            'application_id' => $app1->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('cluster_application', [
            'cluster_id' => $this->cluster->id,
            'application_id' => $app2->id,
            'is_active' => true,
        ]);
    }

    public function test_toggle_cluster_app_status(): void
    {
        $app = Application::create([
            'name' => 'Tour Booking',
            'slug' => 'tour-booking',
            'code' => 'TOUR_BOOK',
            'type' => 'web',
            'is_active' => true,
        ]);

        $this->cluster->applications()->attach($app->id, ['is_active' => true]);

        $response = $this->actingAs($this->user)->patchJson("/admin/clusters/{$this->cluster->id}/apps/{$app->id}/toggle");

        $response->assertOk()
            ->assertJson([
                'message' => 'Application deactivated in cluster.',
                'is_active' => false,
            ]);

        $this->assertDatabaseHas('cluster_application', [
            'cluster_id' => $this->cluster->id,
            'application_id' => $app->id,
            'is_active' => false,
        ]);
    }

    public function test_store_country(): void
    {
        $response = $this->actingAs($this->user)->postJson('/admin/countries', [
            'name' => 'Vietnam',
            'code' => 'VNM',
            'code_alpha2' => 'VN',
            'currency_code' => 'VND',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'default_locale' => 'vi',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Country created successfully.',
            ])
            ->assertJsonStructure([
                'message',
                'country' => ['id', 'name', 'code'],
            ]);

        $this->assertDatabaseHas('countries', [
            'name' => 'Vietnam',
            'code' => 'VNM',
            'code_alpha2' => 'VN',
        ]);
    }

    public function test_update_country(): void
    {
        $response = $this->actingAs($this->user)->putJson("/admin/countries/{$this->country->id}", [
            'name' => 'Thailand Updated',
            'currency_code' => 'THB',
            'timezone' => 'Asia/Bangkok',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Country updated successfully.',
            ])
            ->assertJsonStructure([
                'message',
                'country' => ['id', 'name'],
            ]);

        $this->assertDatabaseHas('countries', [
            'id' => $this->country->id,
            'name' => 'Thailand Updated',
        ]);
    }

    public function test_toggle_country_active_status(): void
    {
        $this->assertTrue($this->country->is_active);

        $response = $this->actingAs($this->user)->patchJson("/admin/countries/{$this->country->id}/toggle");

        $response->assertOk()
            ->assertJson([
                'message' => 'Country deactivated.',
                'is_active' => false,
            ]);

        $this->assertDatabaseHas('countries', [
            'id' => $this->country->id,
            'is_active' => false,
        ]);
    }

    public function test_unauthenticated_cannot_access_admin_clusters(): void
    {
        $response = $this->postJson('/admin/clusters', [
            'name' => 'Test Cluster',
            'country_id' => $this->country->id,
        ]);

        $response->assertUnauthorized();
    }
}
