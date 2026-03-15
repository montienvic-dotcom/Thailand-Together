<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MobileDiscoverApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    private Cluster $cluster;

    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::create([
            'name' => 'Thailand', 'code' => 'THA',
            'code_alpha2' => 'TH', 'is_active' => true,
        ]);

        $this->cluster = Cluster::create([
            'name' => 'Pattaya', 'slug' => 'pattaya', 'code' => 'PTY',
            'country_id' => $country->id, 'is_active' => true,
            'timezone' => 'Asia/Bangkok',
        ]);

        $this->user = User::factory()->create(['status' => 'active']);
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'X-Cluster-Id' => (string) $this->cluster->id,
        ];
    }

    // ── Discover Endpoints ──

    public function test_discover_journeys_requires_auth(): void
    {
        $response = $this->getJson('/api/mobile/discover/journeys');
        $response->assertUnauthorized();
    }

    public function test_discover_journeys_returns_paginated_list(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/mobile/discover/journeys');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta' => ['total', 'limit', 'offset']]);
    }

    public function test_discover_merchants_returns_paginated_list(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/mobile/discover/merchants');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta' => ['total', 'limit', 'offset']]);
    }

    public function test_discover_merchants_nearby_validates_coordinates(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/mobile/discover/merchants/nearby');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lat', 'lng']);
    }

    public function test_discover_merchants_nearby_with_valid_coords(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/mobile/discover/merchants/nearby?lat=12.9236&lng=100.8825&radius=10');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta' => ['center', 'radius_km', 'count']]);
    }

    public function test_discover_merchant_detail_not_found(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/mobile/discover/merchants/999999');

        $response->assertNotFound();
    }

    public function test_discover_recommendations_returns_structure(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/mobile/discover/recommendations');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['cross_cluster', 'explore_other_clusters', 'campaigns'],
            ]);
    }

    // ── Booking Endpoints ──

    public function test_bookings_list_requires_auth(): void
    {
        $response = $this->getJson('/api/mobile/bookings');
        $response->assertUnauthorized();
    }

    public function test_bookings_list_returns_empty_for_new_user(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/mobile/bookings');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta' => ['total', 'limit', 'offset']])
            ->assertJsonPath('meta.total', 0);
    }

    public function test_create_booking_validates_required_fields(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/mobile/bookings', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['merchant_id', 'booking_date']);
    }

    public function test_create_booking_successfully(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/mobile/bookings', [
                'merchant_id' => 1,
                'booking_date' => now()->addDay()->toDateString(),
                'party_size' => 2,
                'notes' => 'Window seat please',
            ]);

        $response->assertCreated()
            ->assertJsonStructure(['data', 'message']);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'merchant_id' => 1,
            'status' => 'upcoming',
        ]);
    }

    public function test_create_booking_with_items(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/mobile/bookings', [
                'merchant_id' => 1,
                'booking_date' => now()->addDay()->toDateString(),
                'items' => [
                    ['name' => 'Thai Massage 60min', 'quantity' => 2, 'unit_price' => 500],
                    ['name' => 'Foot Massage 30min', 'quantity' => 1, 'unit_price' => 300],
                ],
            ]);

        $response->assertCreated();

        $booking = DB::table('bookings')->where('user_id', $this->user->id)->first();
        $this->assertEquals(1300, $booking->total_amount); // 2*500 + 1*300

        $items = DB::table('booking_items')->where('booking_id', $booking->id)->get();
        $this->assertCount(2, $items);
    }

    public function test_cancel_booking(): void
    {
        $bookingId = DB::table('bookings')->insertGetId([
            'booking_code' => 'BK-TEST1234',
            'user_id' => $this->user->id,
            'merchant_id' => 1,
            'booking_date' => now()->addDay(),
            'status' => 'upcoming',
            'total_amount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->putJson("/api/mobile/bookings/{$bookingId}/cancel", [
                'reason' => 'Change of plans',
            ]);

        $response->assertOk()
            ->assertJson(['message' => 'Booking cancelled']);

        $this->assertDatabaseHas('bookings', [
            'id' => $bookingId,
            'status' => 'cancelled',
        ]);
    }

    public function test_cannot_cancel_other_users_booking(): void
    {
        $otherUser = User::factory()->create();
        $bookingId = DB::table('bookings')->insertGetId([
            'booking_code' => 'BK-OTHER123',
            'user_id' => $otherUser->id,
            'merchant_id' => 1,
            'booking_date' => now()->addDay(),
            'status' => 'upcoming',
            'total_amount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->putJson("/api/mobile/bookings/{$bookingId}/cancel");

        $response->assertNotFound();
    }

    // ── Deal Endpoints ──

    public function test_deals_list_requires_auth(): void
    {
        $response = $this->getJson('/api/mobile/deals');
        $response->assertUnauthorized();
    }

    public function test_deals_list_returns_active_deals(): void
    {
        DB::table('deals')->insert([
            'cluster_id' => $this->cluster->id,
            'title_en' => '50% off Spa',
            'title_th' => 'ลด 50% สปา',
            'is_active' => true,
            'priority' => 1,
            'max_redemptions' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/mobile/deals');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta']);
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    public function test_deal_detail_not_found(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/mobile/deals/999999');

        $response->assertNotFound();
    }

    public function test_redeem_deal_successfully(): void
    {
        $dealId = DB::table('deals')->insertGetId([
            'cluster_id' => $this->cluster->id,
            'title_en' => 'Free Drink',
            'title_th' => 'เครื่องดื่มฟรี',
            'is_active' => true,
            'priority' => 1,
            'max_redemptions' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->postJson("/api/mobile/deals/{$dealId}/redeem");

        $response->assertCreated()
            ->assertJson(['message' => 'Deal redeemed successfully']);

        $this->assertDatabaseHas('deal_redemptions', [
            'deal_id' => $dealId,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_cannot_redeem_deal_twice(): void
    {
        $dealId = DB::table('deals')->insertGetId([
            'cluster_id' => $this->cluster->id,
            'title_en' => 'One Time Offer',
            'title_th' => 'โปรครั้งเดียว',
            'is_active' => true,
            'priority' => 1,
            'max_redemptions' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('deal_redemptions')->insert([
            'deal_id' => $dealId,
            'user_id' => $this->user->id,
            'redeemed_at' => now(),
            'created_at' => now(),
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->postJson("/api/mobile/deals/{$dealId}/redeem");

        $response->assertStatus(422)
            ->assertJson(['error' => 'Deal already redeemed']);
    }
}
