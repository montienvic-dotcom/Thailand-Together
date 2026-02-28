<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use App\Services\Permission\PermissionResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClusterController extends Controller
{
    public function __construct(
        private PermissionResolver $permissionResolver,
    ) {}

    /**
     * List all countries with their clusters.
     */
    public function countries(): JsonResponse
    {
        $countries = Country::active()
            ->with(['activeClusters' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $countries]);
    }

    /**
     * List clusters accessible to the current user.
     */
    public function accessibleClusters(Request $request): JsonResponse
    {
        $user = $request->user();
        $clusterIds = $this->permissionResolver->accessibleClusters($user);

        $clusters = Cluster::active()
            ->whereIn('id', $clusterIds)
            ->with('country')
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $clusters]);
    }

    /**
     * Get cluster detail with available apps for the user.
     */
    public function show(Request $request, int $clusterId): JsonResponse
    {
        $cluster = Cluster::active()->with('country')->findOrFail($clusterId);

        $user = $request->user();
        $accessMap = $this->permissionResolver->getAccessMap($user, $clusterId);

        $apps = $cluster->activeApplications()
            ->whereIn('applications.id', array_keys($accessMap))
            ->with(['activeModules' => fn ($q) => $q->whereIn('id', collect($accessMap)->flatten())])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => [
                'cluster' => $cluster,
                'applications' => $apps,
            ],
        ]);
    }
}
