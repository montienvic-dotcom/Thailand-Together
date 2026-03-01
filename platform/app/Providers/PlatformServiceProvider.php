<?php

namespace App\Providers;

use App\Services\ApiGateway\ApiGatewayService;
use App\Services\Cluster\ClusterManager;
use App\Services\CrossCluster\RewardService;
use App\Services\Permission\PermissionResolver;
use App\Services\SSO\SsoService;
use Illuminate\Support\ServiceProvider;

class PlatformServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ClusterManager as singleton (one context per request)
        $this->app->singleton(ClusterManager::class);

        // Permission resolver as singleton
        $this->app->singleton(PermissionResolver::class);

        // SSO service
        $this->app->singleton(SsoService::class);

        // API Gateway service
        $this->app->singleton(ApiGatewayService::class);

        // Reward service
        $this->app->singleton(RewardService::class);
    }

    public function boot(): void
    {
        // Register middleware aliases
        $router = $this->app['router'];
        $router->aliasMiddleware('cluster.aware', \App\Http\Middleware\ClusterAware::class);
        $router->aliasMiddleware('module.access', \App\Http\Middleware\CheckModuleAccess::class);

        // Load admin routes
        $this->loadRoutesFrom(base_path('routes/admin.php'));
    }
}
