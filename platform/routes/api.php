<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClusterController;
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
