<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\Group;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Services\Permission\PermissionResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Admin controller for managing users, groups, roles, and permissions.
 * Supports the 5-level hierarchy: Global > Country > Cluster > App > Module
 */
class PermissionController extends Controller
{
    public function __construct(
        private PermissionResolver $permissionResolver,
    ) {}

    /**
     * List groups with their user counts.
     */
    public function listGroups(Request $request): JsonResponse
    {
        $query = Group::withCount('users');

        if ($clusterId = $request->query('cluster_id')) {
            $query->forCluster((int) $clusterId);
        } elseif ($countryId = $request->query('country_id')) {
            $query->forCountry((int) $countryId);
        }

        return response()->json(['data' => $query->orderBy('sort_order')->get()]);
    }

    /**
     * Set app+module access for a specific user in a cluster.
     *
     * Example body:
     * {
     *   "user_id": 1,
     *   "cluster_id": 1,
     *   "access": [
     *     {"application_id": 1, "module_ids": [1, 2, 4]},
     *     {"application_id": 3, "module_ids": [1, 5, 8]}
     *   ]
     * }
     */
    public function setUserAccess(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'cluster_id' => 'required|exists:clusters,id',
            'access' => 'required|array',
            'access.*.application_id' => 'required|exists:applications,id',
            'access.*.module_ids' => 'required|array',
            'access.*.module_ids.*' => 'exists:modules,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = $request->input('user_id');
        $clusterId = $request->input('cluster_id');

        DB::transaction(function () use ($userId, $clusterId, $request) {
            // Clear existing user-level access for this cluster
            DB::table('user_app_access')
                ->where('user_id', $userId)
                ->where('cluster_id', $clusterId)
                ->delete();

            DB::table('user_module_access')
                ->where('user_id', $userId)
                ->where('cluster_id', $clusterId)
                ->delete();

            // Set new access
            foreach ($request->input('access') as $appAccess) {
                DB::table('user_app_access')->insert([
                    'user_id' => $userId,
                    'cluster_id' => $clusterId,
                    'application_id' => $appAccess['application_id'],
                    'has_access' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($appAccess['module_ids'] as $moduleId) {
                    DB::table('user_module_access')->insert([
                        'user_id' => $userId,
                        'cluster_id' => $clusterId,
                        'module_id' => $moduleId,
                        'has_access' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        // Clear permission cache
        $this->permissionResolver->clearCache(User::find($userId));

        return response()->json(['message' => 'Access updated successfully']);
    }

    /**
     * Set app+module access for a group in a cluster.
     */
    public function setGroupAccess(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'cluster_id' => 'required|exists:clusters,id',
            'access' => 'required|array',
            'access.*.application_id' => 'required|exists:applications,id',
            'access.*.module_ids' => 'required|array',
            'access.*.module_ids.*' => 'exists:modules,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $groupId = $request->input('group_id');
        $clusterId = $request->input('cluster_id');

        DB::transaction(function () use ($groupId, $clusterId, $request) {
            DB::table('group_app_access')
                ->where('group_id', $groupId)
                ->where('cluster_id', $clusterId)
                ->delete();

            DB::table('group_module_access')
                ->where('group_id', $groupId)
                ->where('cluster_id', $clusterId)
                ->delete();

            foreach ($request->input('access') as $appAccess) {
                DB::table('group_app_access')->insert([
                    'group_id' => $groupId,
                    'cluster_id' => $clusterId,
                    'application_id' => $appAccess['application_id'],
                    'has_access' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($appAccess['module_ids'] as $moduleId) {
                    DB::table('group_module_access')->insert([
                        'group_id' => $groupId,
                        'cluster_id' => $clusterId,
                        'module_id' => $moduleId,
                        'has_access' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        // Clear permission cache for all users in this group
        $this->permissionResolver->clearGroupCache($groupId);

        return response()->json(['message' => 'Group access updated successfully']);
    }

    /**
     * Get the full access map for a user (what they can access where).
     */
    public function getUserAccessMap(Request $request, int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $clusterId = $request->query('cluster_id');

        if ($clusterId) {
            return response()->json([
                'data' => $this->permissionResolver->getAccessMap($user, (int) $clusterId),
            ]);
        }

        // All clusters
        $clusterIds = $this->permissionResolver->accessibleClusters($user);
        $map = [];
        foreach ($clusterIds as $cId) {
            $map[$cId] = $this->permissionResolver->getAccessMap($user, $cId);
        }

        return response()->json(['data' => $map]);
    }

    /**
     * Assign a role to a user with scope.
     */
    public function assignRole(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
            'country_id' => 'nullable|exists:countries,id',
            'cluster_id' => 'nullable|exists:clusters,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('role_user')->updateOrInsert(
            [
                'user_id' => $request->input('user_id'),
                'role_id' => $request->input('role_id'),
                'country_id' => $request->input('country_id'),
                'cluster_id' => $request->input('cluster_id'),
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json(['message' => 'Role assigned successfully']);
    }
}
