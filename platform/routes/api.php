<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClusterController;
use App\Http\Controllers\Api\IntegrationController;
use App\Http\Controllers\Api\Merchant\JourneyController;
use App\Http\Controllers\Api\Merchant\MerchantController;
use App\Http\Controllers\Api\Mobile\NotificationController;
use App\Http\Controllers\Api\Mobile\ProfileController;
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

    // ── Mobile App Routes (App Together) ──
    Route::prefix('mobile')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::get('/profile/settings', [ProfileController::class, 'settings']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
        Route::post('/device-token', [NotificationController::class, 'registerDeviceToken']);
    });

    // ── Third-Party Integration Routes ──
    Route::middleware('cluster.aware')->prefix('integrations')->group(function () {
        // Payment
        Route::post('/payment/create', [IntegrationController::class, 'createPayment']);
        Route::get('/payment/{paymentId}/status', [IntegrationController::class, 'paymentStatus']);
        Route::post('/payment/{paymentId}/refund', [IntegrationController::class, 'refundPayment']);

        // SMS
        Route::post('/sms/send', [IntegrationController::class, 'sendSMS']);
        Route::post('/sms/otp', [IntegrationController::class, 'sendOtp']);

        // AI Services
        Route::post('/ai/chat', [IntegrationController::class, 'aiChat']);
        Route::post('/ai/translate', [IntegrationController::class, 'aiTranslate']);
        Route::post('/ai/tts', [IntegrationController::class, 'aiTextToSpeech']);

        // Cloud Point / Rewards
        Route::post('/points/earn', [IntegrationController::class, 'pointsEarn']);
        Route::post('/points/redeem', [IntegrationController::class, 'pointsRedeem']);
        Route::get('/points/balance/{userId}', [IntegrationController::class, 'pointsBalance']);
        Route::post('/points/transfer', [IntegrationController::class, 'pointsTransfer']);

        // Translation (dedicated)
        Route::post('/translate', [IntegrationController::class, 'translate']);
        Route::post('/translate/batch', [IntegrationController::class, 'translateBatch']);
        Route::post('/translate/detect', [IntegrationController::class, 'detectLanguage']);

        // TTS (dedicated)
        Route::post('/tts/synthesize', [IntegrationController::class, 'ttsSynthesize']);
        Route::get('/tts/voices', [IntegrationController::class, 'ttsVoices']);

        // HelpDesk
        Route::post('/helpdesk/tickets', [IntegrationController::class, 'createTicket']);
        Route::get('/helpdesk/tickets/{ticketId}', [IntegrationController::class, 'getTicket']);
        Route::post('/helpdesk/tickets/{ticketId}/comment', [IntegrationController::class, 'addTicketComment']);

        // Health check
        Route::get('/health', [IntegrationController::class, 'health']);
    });

    // ── Cluster-Scoped Routes (auth + cluster context) ──
    Route::middleware('cluster.aware')->group(function () {
        Route::get('/clusters/{clusterId}', [ClusterController::class, 'show']);

        // Super App Menu
        Route::get('/menu', [MenuController::class, 'index']);
    });
});
