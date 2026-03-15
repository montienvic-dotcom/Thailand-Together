<?php

use App\Http\Controllers\Admin\AdminApiProviderController;
use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Admin\AdminClusterController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPermissionController;
use App\Http\Controllers\Admin\AdminUserController;
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

    // ── Applications CRUD ──
    Route::get('/applications', [AdminWebController::class, 'applications'])->name('applications');
    Route::get('/applications/{application}', [AdminWebController::class, 'applicationDetail'])->name('applications.show');
    Route::post('/applications', [AdminApplicationController::class, 'store'])->name('applications.store');
    Route::put('/applications/{application}', [AdminApplicationController::class, 'update'])->name('applications.update');
    Route::patch('/applications/{application}/toggle', [AdminApplicationController::class, 'toggleActive'])->name('applications.toggle');
    Route::delete('/applications/{application}', [AdminApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::post('/applications/{application}/reorder-modules', [AdminApplicationController::class, 'reorderModules'])->name('applications.reorder-modules');

    // ── Modules CRUD ──
    Route::post('/applications/{application}/modules', [AdminApplicationController::class, 'storeModule'])->name('modules.store');
    Route::put('/modules/{module}', [AdminApplicationController::class, 'updateModule'])->name('modules.update');
    Route::patch('/modules/{module}/toggle', [AdminApplicationController::class, 'toggleModule'])->name('modules.toggle');
    Route::patch('/modules/{module}/toggle-premium', [AdminApplicationController::class, 'toggleModulePremium'])->name('modules.toggle-premium');
    Route::delete('/modules/{module}', [AdminApplicationController::class, 'destroyModule'])->name('modules.destroy');

    // ── Users CRUD ──
    Route::get('/users', [AdminWebController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminWebController::class, 'userDetail'])->name('users.show');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/groups', [AdminUserController::class, 'updateGroups'])->name('users.update-groups');
    Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/json', [AdminUserController::class, 'show'])->name('users.json');

    // ── API Providers CRUD ──
    Route::get('/api-providers', [AdminWebController::class, 'apiProviders'])->name('api-providers');
    Route::get('/api-providers/{provider}', [AdminWebController::class, 'apiProviderDetail'])->name('api-providers.show');
    Route::post('/api-providers', [AdminApiProviderController::class, 'store'])->name('api-providers.store');
    Route::put('/api-providers/{provider}', [AdminApiProviderController::class, 'update'])->name('api-providers.update');
    Route::patch('/api-providers/{provider}/toggle', [AdminApiProviderController::class, 'toggleActive'])->name('api-providers.toggle');
    Route::delete('/api-providers/{provider}', [AdminApiProviderController::class, 'destroy'])->name('api-providers.destroy');
    Route::get('/api-providers/{provider}/health', [AdminApiProviderController::class, 'healthCheck'])->name('api-providers.health');

    // ── API Credentials ──
    Route::post('/api-providers/{provider}/credentials', [AdminApiProviderController::class, 'storeCredential'])->name('api-credentials.store');
    Route::put('/api-credentials/{credential}', [AdminApiProviderController::class, 'updateCredential'])->name('api-credentials.update');
    Route::delete('/api-credentials/{credential}', [AdminApiProviderController::class, 'destroyCredential'])->name('api-credentials.destroy');

    // ── Clusters & Countries ──
    Route::get('/clusters', [AdminWebController::class, 'clusters'])->name('clusters');
    Route::get('/clusters/{cluster}', [AdminWebController::class, 'clusterDetail'])->name('clusters.show');
    Route::post('/clusters', [AdminClusterController::class, 'store'])->name('clusters.store');
    Route::put('/clusters/{cluster}', [AdminClusterController::class, 'update'])->name('clusters.update');
    Route::patch('/clusters/{cluster}/toggle', [AdminClusterController::class, 'toggleActive'])->name('clusters.toggle');
    Route::delete('/clusters/{cluster}', [AdminClusterController::class, 'destroy'])->name('clusters.destroy');
    Route::put('/clusters/{cluster}/sync-apps', [AdminClusterController::class, 'syncApplications'])->name('clusters.sync-apps');
    Route::patch('/clusters/{cluster}/apps/{application}/toggle', [AdminClusterController::class, 'toggleClusterApp'])->name('clusters.toggle-app');

    Route::post('/countries', [AdminClusterController::class, 'storeCountry'])->name('countries.store');
    Route::put('/countries/{country}', [AdminClusterController::class, 'updateCountry'])->name('countries.update');
    Route::patch('/countries/{country}/toggle', [AdminClusterController::class, 'toggleCountry'])->name('countries.toggle');

    // ── Permissions ──
    Route::get('/permissions', [AdminWebController::class, 'permissions'])->name('permissions');
    Route::get('/permissions/users', [AdminWebController::class, 'permissionUsers'])->name('permissions.users');
    Route::get('/permissions/groups', [AdminWebController::class, 'permissionGroups'])->name('permissions.groups');
    Route::get('/permissions/roles', [AdminWebController::class, 'permissionRoles'])->name('permissions.roles');

    // Groups CRUD
    Route::post('/permissions/groups', [AdminPermissionController::class, 'storeGroup'])->name('permissions.groups.store');
    Route::put('/permissions/groups/{group}', [AdminPermissionController::class, 'updateGroup'])->name('permissions.groups.update');
    Route::patch('/permissions/groups/{group}/toggle', [AdminPermissionController::class, 'toggleGroup'])->name('permissions.groups.toggle');
    Route::delete('/permissions/groups/{group}', [AdminPermissionController::class, 'destroyGroup'])->name('permissions.groups.destroy');

    // Roles CRUD
    Route::post('/permissions/roles', [AdminPermissionController::class, 'storeRole'])->name('permissions.roles.store');
    Route::put('/permissions/roles/{role}', [AdminPermissionController::class, 'updateRole'])->name('permissions.roles.update');
    Route::delete('/permissions/roles/{role}', [AdminPermissionController::class, 'destroyRole'])->name('permissions.roles.destroy');
    Route::get('/permissions/roles/{role}/permissions', [AdminPermissionController::class, 'getRolePermissions'])->name('permissions.roles.permissions');
    Route::put('/permissions/roles/{role}/sync-permissions', [AdminPermissionController::class, 'toggleRolePermissions'])->name('permissions.roles.sync-permissions');

    // ── Reference ──
    Route::get('/api-reference', [AdminWebController::class, 'apiReference'])->name('api-reference');
    Route::get('/roadmap', [AdminWebController::class, 'roadmap'])->name('roadmap');
});
