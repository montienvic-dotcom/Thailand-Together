<?php

namespace App\Http\Middleware;

use App\Models\Global\Cluster;
use App\Services\Cluster\ClusterManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the current cluster context from the request.
 * Checks header X-Cluster-Id, subdomain, or route parameter.
 */
class ClusterAware
{
    public function __construct(
        private ClusterManager $clusterManager,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $cluster = $this->resolveCluster($request);

        if (!$cluster) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Cluster not found or inactive',
                    'message' => 'Please specify a valid cluster via X-Cluster-Id header, cluster parameter, or subdomain.',
                ], 400);
            }

            return redirect()->route('superapp.landing')
                ->with('error', 'Please select a cluster.');
        }

        $this->clusterManager->setCluster($cluster);

        // Share with views
        view()->share('currentCluster', $cluster);
        view()->share('currentCountry', $cluster->country);

        return $next($request);
    }

    private function resolveCluster(Request $request): ?Cluster
    {
        // 1. From header (API calls)
        if ($clusterId = $request->header('X-Cluster-Id')) {
            return Cluster::active()->find($clusterId);
        }

        // 2. From route/query parameter
        if ($clusterSlug = $request->route('cluster') ?? $request->query('cluster')) {
            return Cluster::active()->where('slug', $clusterSlug)->first();
        }

        // 3. From subdomain (e.g., pattaya.thailandtogether.com)
        $host = $request->getHost();
        $parts = explode('.', $host);
        if (count($parts) >= 3) {
            $subdomain = $parts[0];
            return Cluster::active()->where('slug', $subdomain)->first();
        }

        // 4. Default: first active cluster (development fallback)
        if (app()->environment('local', 'testing')) {
            return Cluster::active()->first();
        }

        return null;
    }
}
