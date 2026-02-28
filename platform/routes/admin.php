<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes - Thailand Together Backend
|--------------------------------------------------------------------------
|
| All admin routes require authentication and admin-level roles.
| Routes are cluster-aware for scoped administration.
|
*/

Route::middleware(['auth:sanctum', 'cluster.aware'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Permission Management
    Route::prefix('permissions')->group(function () {
        Route::get('/groups', [PermissionController::class, 'listGroups']);
        Route::post('/user-access', [PermissionController::class, 'setUserAccess']);
        Route::post('/group-access', [PermissionController::class, 'setGroupAccess']);
        Route::get('/user-access-map/{userId}', [PermissionController::class, 'getUserAccessMap']);
        Route::post('/assign-role', [PermissionController::class, 'assignRole']);
    });
});
