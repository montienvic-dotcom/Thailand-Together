<?php

namespace App\Services\Permission;

use App\Models\Auth\User;
use App\Models\Global\Cluster;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Central permission resolver.
 * Resolves user access through the 5-level hierarchy:
 * Global → Country → Cluster → App → Module
 *
 * Resolution priority (most specific wins):
 * 1. User-specific override
 * 2. Group-level access
 * 3. Role-based access
 *
 * Cache strategy:
 * - When the cache driver supports tags (Redis, Memcached): uses Cache::tags()
 *   for efficient bulk invalidation per user.
 * - When tags are not supported (database, file, array): uses a generation
 *   counter embedded in cache keys. Incrementing the generation invalidates
 *   all previous entries (they expire naturally via TTL).
 */
class PermissionResolver
{
    private const CACHE_TTL = 300; // 5 minutes

    private ?bool $tagSupport = null;

    /**
     * Check if a user can access a specific module in a cluster.
     */
    public function canAccess(User $user, int $clusterId, int $applicationId, ?int $moduleId = null): bool
    {
        $suffix = "{$clusterId}:{$applicationId}:" . ($moduleId ?? 'null');
        $cacheKey = $this->buildKey($user->id, "perm:{$suffix}");

        return $this->remember($user->id, $cacheKey, function () use ($user, $clusterId, $applicationId, $moduleId) {
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
        $cacheKey = $this->buildKey($user->id, "access_map:{$clusterId}");

        return $this->remember($user->id, $cacheKey, function () use ($user, $clusterId) {
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
        $cacheKey = $this->buildKey($user->id, 'clusters');

        return $this->remember($user->id, $cacheKey, function () use ($user) {
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
            $appClusters = DB::table('user_app_access')
                ->where('user_id', $user->id)
                ->where('has_access', true)
                ->pluck('cluster_id');

            // Clusters from group access
            $groupIds = $user->groups()->pluck('groups.id');
            $groupClusters = collect();
            if ($groupIds->isNotEmpty()) {
                $groupClusters = DB::table('group_app_access')
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
        if ($this->supportsTags()) {
            Cache::tags(["user_perms:{$user->id}"])->flush();

            return;
        }

        // Increment generation — old keys become stale and expire via TTL
        $gen = (int) Cache::get($this->generationKey($user->id), 0);
        Cache::forever($this->generationKey($user->id), $gen + 1);
    }

    /**
     * Clear permission cache for all users in a group.
     */
    public function clearGroupCache(int $groupId): void
    {
        $userIds = DB::table('group_user')
            ->where('group_id', $groupId)
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $this->clearCache(User::find($userId));
        }
    }

    // ── Private helpers ──

    private function supportsTags(): bool
    {
        if ($this->tagSupport === null) {
            $this->tagSupport = Cache::getStore() instanceof TaggableStore;
        }

        return $this->tagSupport;
    }

    private function generationKey(int $userId): string
    {
        return "perm_gen:{$userId}";
    }

    private function buildKey(int $userId, string $suffix): string
    {
        if ($this->supportsTags()) {
            return $suffix;
        }

        $gen = (int) Cache::get($this->generationKey($userId), 0);

        return "perm:v{$gen}:{$userId}:{$suffix}";
    }

    private function remember(int $userId, string $cacheKey, \Closure $callback): mixed
    {
        if ($this->supportsTags()) {
            return Cache::tags(["user_perms:{$userId}"])->remember($cacheKey, self::CACHE_TTL, $callback);
        }

        return Cache::remember($cacheKey, self::CACHE_TTL, $callback);
    }
}
