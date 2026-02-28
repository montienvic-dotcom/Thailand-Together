<?php

namespace App\Services\Permission;

use App\Models\Auth\User;
use App\Models\Global\Cluster;
use Illuminate\Support\Facades\Cache;

/**
 * Central permission resolver.
 * Resolves user access through the 5-level hierarchy:
 * Global → Country → Cluster → App → Module
 *
 * Resolution priority (most specific wins):
 * 1. User-specific override
 * 2. Group-level access
 * 3. Role-based access
 */
class PermissionResolver
{
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Check if a user can access a specific module in a cluster.
     */
    public function canAccess(User $user, int $clusterId, int $applicationId, ?int $moduleId = null): bool
    {
        $cacheKey = "perm:{$user->id}:{$clusterId}:{$applicationId}:" . ($moduleId ?? 'null');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $clusterId, $applicationId, $moduleId) {
            // Step 1: Admin shortcut
            if ($user->isClusterAdmin($clusterId)) {
                return true;
            }

            // Step 2: Check app-level access
            if (!$user->canAccessApp($applicationId, $clusterId)) {
                return false;
            }

            // Step 3: If module specified, check module access
            if ($moduleId !== null) {
                return $user->canAccessModule($moduleId, $clusterId);
            }

            return true;
        });
    }

    /**
     * Get full access map for a user in a cluster.
     * Returns: [app_id => [module_ids...], ...]
     */
    public function getAccessMap(User $user, int $clusterId): array
    {
        $cacheKey = "access_map:{$user->id}:{$clusterId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $clusterId) {
            $appIds = $user->accessibleAppIds($clusterId);
            $moduleIds = $user->accessibleModuleIds($clusterId);

            $map = [];
            foreach ($appIds as $appId) {
                $map[$appId] = \App\Models\App\Module::where('application_id', $appId)
                    ->whereIn('id', $moduleIds)
                    ->where('is_active', true)
                    ->pluck('id')
                    ->toArray();
            }

            return $map;
        });
    }

    /**
     * Get accessible clusters for a user.
     */
    public function accessibleClusters(User $user): array
    {
        $cacheKey = "clusters:{$user->id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
            if ($user->isGlobalAdmin()) {
                return Cluster::active()->pluck('id')->toArray();
            }

            // Clusters from roles
            $roleClusters = $user->roles()
                ->whereNotNull('role_user.cluster_id')
                ->pluck('role_user.cluster_id');

            // Clusters from country-level roles
            $countryIds = $user->roles()
                ->whereNotNull('role_user.country_id')
                ->pluck('role_user.country_id');

            $countryClusters = Cluster::active()
                ->whereIn('country_id', $countryIds)
                ->pluck('id');

            // Clusters from app access
            $appClusters = \Illuminate\Support\Facades\DB::table('user_app_access')
                ->where('user_id', $user->id)
                ->where('has_access', true)
                ->pluck('cluster_id');

            // Clusters from group access
            $groupIds = $user->groups()->pluck('groups.id');
            $groupClusters = collect();
            if ($groupIds->isNotEmpty()) {
                $groupClusters = \Illuminate\Support\Facades\DB::table('group_app_access')
                    ->whereIn('group_id', $groupIds)
                    ->where('has_access', true)
                    ->pluck('cluster_id');
            }

            return $roleClusters
                ->merge($countryClusters)
                ->merge($appClusters)
                ->merge($groupClusters)
                ->unique()
                ->values()
                ->toArray();
        });
    }

    /**
     * Clear permission cache for a user.
     */
    public function clearCache(User $user): void
    {
        // In production, use cache tags for efficient invalidation.
        // For now, we rely on TTL-based expiration.
        Cache::forget("clusters:{$user->id}");
    }
}
