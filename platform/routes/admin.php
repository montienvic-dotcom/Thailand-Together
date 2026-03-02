<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminWebController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes - Thailand Together Backend
|--------------------------------------------------------------------------
|
| API routes use auth:sanctum for token-based auth.
| Web routes use session auth for Blade views.
|
*/

// ── API Routes (token-based, JSON responses) ──
Route::middleware(['auth:sanctum', 'cluster.aware'])->prefix('admin/api')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    Route::prefix('permissions')->group(function () {
        Route::get('/groups', [PermissionController::class, 'listGroups']);
        Route::post('/user-access', [PermissionController::class, 'setUserAccess']);
        Route::post('/group-access', [PermissionController::class, 'setGroupAccess']);
        Route::get('/user-access-map/{userId}', [PermissionController::class, 'getUserAccessMap']);
        Route::post('/assign-role', [PermissionController::class, 'assignRole']);
    });
});

// ── Web Routes (session-based, Blade views) ──
Route::middleware('web')->prefix('admin')->name('admin.')->group(function () {
    // Login (no auth required)
    Route::get('/login', [AdminWebController::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminWebController::class, 'login'])->name('login.submit');
});

Route::middleware(['web', 'auth', 'cluster.aware'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/applications', [AdminWebController::class, 'applications'])->name('applications');
    Route::get('/applications/{application}', [AdminWebController::class, 'applicationDetail'])->name('applications.show');
    Route::get('/permissions', [AdminWebController::class, 'permissions'])->name('permissions');
    Route::get('/permissions/users', [AdminWebController::class, 'permissionUsers'])->name('permissions.users');
    Route::get('/permissions/groups', [AdminWebController::class, 'permissionGroups'])->name('permissions.groups');
    Route::get('/permissions/roles', [AdminWebController::class, 'permissionRoles'])->name('permissions.roles');
    Route::get('/api-reference', [AdminWebController::class, 'apiReference'])->name('api-reference');
    Route::get('/roadmap', [AdminWebController::class, 'roadmap'])->name('roadmap');
});
