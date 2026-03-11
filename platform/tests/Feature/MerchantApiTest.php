<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::create(['name' => 'Thailand', 'code' => 'THA', 'code_alpha2' => 'TH', 'is_active' => true]);
        Cluster::create([
            'name' => 'Pattaya', 'slug' => 'pattaya', 'code' => 'PTY',
            'country_id' => $country->id, 'is_active' => true, 'timezone' => 'Asia/Bangkok',
        ]);
    }

    // Note: Search, reviews, and checkin tests that depend on MySQL views
    // (vw_merchant_search_public, etc.) are skipped in SQLite test environment.
    // These should be tested in integration tests with MySQL.

    public function test_merchant_checkin_validates_required_fields(): void
    {
        $response = $this->postJson('/api/merchant/checkin', []);

        $response->assertStatus(422);
    }

    public function test_merchant_favorite_toggle_validates_fields(): void
    {
        $response = $this->postJson('/api/merchant/favorite/toggle', []);

        $response->assertStatus(422);
    }

    public function test_merchant_wishlist_toggle_validates_fields(): void
    {
        $response = $this->postJson('/api/merchant/wishlist/toggle', []);

        $response->assertStatus(422);
    }

    public function test_merchant_review_validates_fields(): void
    {
        $response = $this->postJson('/api/merchant/review', []);

        $response->assertStatus(422);
    }

    public function test_merchant_api_endpoints_exist(): void
    {
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->map(fn ($r) => $r->uri());

        $this->assertTrue($routes->contains('api/merchants/search'));
        $this->assertTrue($routes->contains('api/merchant/checkin'));
        $this->assertTrue($routes->contains('api/merchant/favorite/toggle'));
        $this->assertTrue($routes->contains('api/merchant/wishlist/toggle'));
        $this->assertTrue($routes->contains('api/merchant/review'));
        $this->assertTrue($routes->contains('api/merchant/{merchant_id}/reviews'));
    }

    public function test_mobile_api_endpoints_exist(): void
    {
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->map(fn ($r) => $r->uri());

        $this->assertTrue($routes->contains('api/mobile/profile'));
        $this->assertTrue($routes->contains('api/mobile/notifications'));
        $this->assertTrue($routes->contains('api/mobile/device-token'));
    }

    public function test_integration_api_endpoints_exist(): void
    {
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->map(fn ($r) => $r->uri());

        $this->assertTrue($routes->contains('api/integrations/payment/create'));
        $this->assertTrue($routes->contains('api/integrations/sms/send'));
        $this->assertTrue($routes->contains('api/integrations/ai/chat'));
        $this->assertTrue($routes->contains('api/integrations/ai/translate'));
        $this->assertTrue($routes->contains('api/integrations/health'));
    }
}
