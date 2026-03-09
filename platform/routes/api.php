<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClusterController;
use App\Http\Controllers\Api\Merchant\JourneyController;
use App\Http\Controllers\Api\Merchant\MerchantController;
use App\Http\Controllers\SuperApp\MenuController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Thailand Together Platform
|--------------------------------------------------------------------------
|
| Public routes (no auth needed)
| Protected routes (SSO token required)
| Cluster-scoped routes (token + cluster context required)
|
*/

// ── Public Routes ──

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/sso', [AuthController::class, 'ssoLogin']);
});

Route::get('/countries', [ClusterController::class, 'countries']);

// ── Merchant & Journey Public APIs ──
// Journey one-call
Route::get('/journeys/{journey_code}/onecall/final', [JourneyController::class, 'oneCallFinal']);
Route::get('/journeys/{journey_code}/merchants/rows', [JourneyController::class, 'merchantRows']);
Route::get('/journeys/{journey_code}/merchants', [JourneyController::class, 'journeyMerchants']);
Route::get('/journeys/{journey_code}/merchant-stats', [JourneyController::class, 'merchantStats']);

// Merchant search (public)
Route::get('/merchants/search', [MerchantController::class, 'search']);

// Merchant by place
Route::get('/places/{place_code}/merchants', [MerchantController::class, 'byPlace']);

// Merchant reviews (public read)
Route::get('/merchant/{merchant_id}/reviews', [MerchantController::class, 'reviews']);

// ── Merchant User Actions (require user_id in body) ──
// Merchant search (user context)
Route::get('/merchants/search/user', [MerchantController::class, 'searchUser']);

// Check-in, Favorite, Wishlist, Review
Route::post('/merchant/checkin', [MerchantController::class, 'checkin']);
Route::post('/merchant/favorite/toggle', [MerchantController::class, 'favoriteToggle']);
Route::post('/merchant/wishlist/toggle', [MerchantController::class, 'wishlistToggle']);
Route::post('/merchant/review', [MerchantController::class, 'createReview']);

// ── Protected Routes (auth required) ──

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/session', [AuthController::class, 'session']);

    Route::get('/clusters/accessible', [ClusterController::class, 'accessibleClusters']);

    // ── Cluster-Scoped Routes (auth + cluster context) ──
    Route::middleware('cluster.aware')->group(function () {
        Route::get('/clusters/{clusterId}', [ClusterController::class, 'show']);

        // Super App Menu
        Route::get('/menu', [MenuController::class, 'index']);
    });
});
