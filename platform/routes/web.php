<?php

use App\Http\Controllers\SuperApp\SuperAppWebController;
use Illuminate\Support\Facades\Route;

// Landing / Cluster selector (no auth)
Route::get('/', [SuperAppWebController::class, 'landing'])->name('superapp.landing');

// Public pages (no auth)
Route::get('/api-docs', [SuperAppWebController::class, 'apiDocs'])->name('superapp.api-docs');
Route::get('/guide', [SuperAppWebController::class, 'guide'])->name('superapp.guide');

// Auth routes
Route::get('/login', [SuperAppWebController::class, 'loginForm'])->name('login');
Route::post('/login', [SuperAppWebController::class, 'login'])->name('login.submit');
Route::post('/logout', [SuperAppWebController::class, 'logout'])->name('logout');

// Cluster-scoped routes (auth + cluster context)
Route::middleware(['auth', 'cluster.aware'])->group(function () {
    Route::get('/cluster/{cluster}', [SuperAppWebController::class, 'clusterHome'])->name('superapp.cluster');
    Route::get('/cluster/{cluster}/app/{application}', [SuperAppWebController::class, 'appDetail'])->name('superapp.app');
});
