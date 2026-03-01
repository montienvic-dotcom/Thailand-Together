<?php

namespace App\Http\Controllers\SuperApp;

use App\Http\Controllers\Controller;
use App\Models\Global\MenuItem;
use App\Services\Cluster\ClusterManager;
use App\Services\Permission\PermissionResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Provides the dynamic Global Header Menu for the Super App.
 * Menu items are filtered by cluster context and user permissions.
 */
class MenuController extends Controller
{
    public function __construct(
        private ClusterManager $clusterManager,
        private PermissionResolver $permissionResolver,
    ) {}

    /**
     * Get the full menu tree for the current user and cluster.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $cluster = $this->clusterManager->current();
        $countryId = $this->clusterManager->countryId();
        $clusterId = $this->clusterManager->currentId();

        // Get all potentially visible menu items for this context
        $items = MenuItem::active()
            ->topLevel()
            ->forContext($countryId, $clusterId)
            ->with(['children' => fn ($q) => $q->active()->forContext($countryId, $clusterId)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        // Filter by user permissions and visibility
        $filtered = $items->filter(fn ($item) => $this->isVisible($item, $user, $clusterId))
            ->map(function ($item) use ($user, $clusterId) {
                $item->children = $item->children->filter(
                    fn ($child) => $this->isVisible($child, $user, $clusterId)
                )->values();
                return $item;
            })
            ->values();

        return response()->json([
            'data' => [
                'menu' => $filtered,
                'cluster' => [
                    'id' => $cluster?->id,
                    'name' => $cluster?->name,
                    'slug' => $cluster?->slug,
                ],
                'country' => [
                    'id' => $this->clusterManager->country()?->id,
                    'name' => $this->clusterManager->country()?->name,
                    'code' => $this->clusterManager->country()?->code_alpha2,
                ],
            ],
        ]);
    }

    /**
     * Check if a menu item should be visible to the user.
     */
    private function isVisible(MenuItem $item, $user, ?int $clusterId): bool
    {
        // Check visibility setting
        $visible = match ($item->visibility) {
            'all' => true,
            'authenticated' => $user !== null,
            'guest' => $user === null,
            'admin' => $user && ($user->isGlobalAdmin() || ($clusterId && $user->isClusterAdmin($clusterId))),
            default => true,
        };

        if (!$visible) {
            return false;
        }

        // Check required permissions (if linked to an app)
        if ($item->application_id && $user && $clusterId) {
            return $this->permissionResolver->canAccess($user, $clusterId, $item->application_id);
        }

        // Check explicit required_permissions
        if (!empty($item->required_permissions) && $user) {
            // At least one permission must match
            foreach ($item->required_permissions as $perm) {
                if ($user->roles()->whereHas('permissions', fn ($q) => $q->where('slug', $perm))->exists()) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }
}
