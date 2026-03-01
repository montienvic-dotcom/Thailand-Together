<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use App\Models\App\Application;
use App\Models\Integration\ApiProvider;
use App\Services\Cluster\ClusterManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Backend admin dashboard.
 * Shows overview based on admin level (global/country/cluster).
 */
class AdminDashboardController extends Controller
{
    public function __construct(
        private ClusterManager $clusterManager,
    ) {}

    /**
     * Dashboard overview data.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'admin_level' => $this->getAdminLevel($user),
        ];

        if ($user->isGlobalAdmin()) {
            $data += $this->globalDashboard();
        } elseif ($this->clusterManager->countryId() && $user->isCountryAdmin($this->clusterManager->countryId())) {
            $data += $this->countryDashboard($this->clusterManager->countryId());
        } elseif ($this->clusterManager->currentId()) {
            $data += $this->clusterDashboard($this->clusterManager->currentId());
        }

        return response()->json(['data' => $data]);
    }

    private function globalDashboard(): array
    {
        return [
            'countries' => Country::active()->count(),
            'clusters' => Cluster::active()->count(),
            'total_users' => User::active()->count(),
            'applications' => Application::active()->count(),
            'api_providers' => ApiProvider::active()->count(),
            'countries_list' => Country::active()
                ->withCount('activeClusters')
                ->orderBy('sort_order')
                ->get(['id', 'name', 'code']),
        ];
    }

    private function countryDashboard(int $countryId): array
    {
        return [
            'clusters' => Cluster::active()->forCountry($countryId)->count(),
            'clusters_list' => Cluster::active()
                ->forCountry($countryId)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'is_active', 'launch_date']),
        ];
    }

    private function clusterDashboard(int $clusterId): array
    {
        $cluster = Cluster::with('country')->findOrFail($clusterId);

        return [
            'cluster' => $cluster,
            'applications' => $cluster->activeApplications()->count(),
            'applications_list' => $cluster->activeApplications()
                ->withCount('activeModules')
                ->orderBy('sort_order')
                ->get(['applications.id', 'applications.name', 'applications.code', 'applications.icon']),
        ];
    }

    private function getAdminLevel($user): string
    {
        if ($user->isGlobalAdmin()) return 'global';

        $countryId = $this->clusterManager->countryId();
        if ($countryId && $user->isCountryAdmin($countryId)) return 'country';

        $clusterId = $this->clusterManager->currentId();
        if ($clusterId && $user->isClusterAdmin($clusterId)) return 'cluster';

        return 'none';
    }
}
