<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\Group;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Admin controller for Groups, Roles, and Permissions CRUD.
 * Supports the 5-level hierarchy: Global > Country > Cluster > App > Module
 */
class AdminPermissionController extends Controller
{
    // ─── Groups ──────────────────────────────────────────────────

    /**
     * Create a new group.
     */
    public function storeGroup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'scope' => 'required|in:global,country,cluster',
            'country_id' => 'nullable|exists:countries,id',
            'cluster_id' => 'nullable|exists:clusters,id',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? ((Group::max('sort_order') ?? 0) + 1);

        $group = Group::create($data);

        return response()->json([
            'message' => 'Group created successfully.',
            'group' => $group,
        ], 201);
    }

    /**
     * Update an existing group.
     */
    public function updateGroup(Request $request, Group $group): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'scope' => 'sometimes|in:global,country,cluster',
            'country_id' => 'nullable|exists:countries,id',
            'cluster_id' => 'nullable|exists:clusters,id',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $group->update($data);

        return response()->json([
            'message' => 'Group updated successfully.',
            'group' => $group->fresh(),
        ]);
    }

    /**
     * Soft-delete a group.
     */
    public function destroyGroup(Group $group): JsonResponse
    {
        $group->delete();

        return response()->json(['message' => 'Group deleted successfully.']);
    }

    /**
     * Toggle a group's is_active status.
     */
    public function toggleGroup(Group $group): JsonResponse
    {
        $group->update(['is_active' => !$group->is_active]);

        return response()->json([
            'message' => $group->is_active ? 'Group activated.' : 'Group deactivated.',
            'is_active' => $group->is_active,
        ]);
    }

    // ─── Roles ───────────────────────────────────────────────────

    /**
     * Create a new role.
     */
    public function storeRole(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|in:global,country,cluster,app',
            'is_system' => 'sometimes|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_system'] = $data['is_system'] ?? false;

        $role = Role::create($data);

        return response()->json([
            'message' => 'Role created successfully.',
            'role' => $role,
        ], 201);
    }

    /**
     * Update an existing role.
     */
    public function updateRole(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'sometimes|in:global,country,cluster,app',
            'is_system' => 'sometimes|boolean',
        ]);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $role->update($data);

        return response()->json([
            'message' => 'Role updated successfully.',
            'role' => $role->fresh(),
        ]);
    }

    /**
     * Delete a role. System roles cannot be deleted.
     */
    public function destroyRole(Role $role): JsonResponse
    {
        if ($role->is_system) {
            return response()->json([
                'message' => 'System roles cannot be deleted.',
            ], 403);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully.']);
    }

    /**
     * Get permission IDs for a role (JSON).
     */
    public function getRolePermissions(Role $role): JsonResponse
    {
        return response()->json([
            'permission_ids' => $role->permissions()->pluck('permissions.id'),
        ]);
    }

    /**
     * Sync the permissions attached to a role via the role_permission pivot.
     */
    public function toggleRolePermissions(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($data['permission_ids']);

        return response()->json([
            'message' => 'Role permissions updated successfully.',
            'role' => $role->load('permissions'),
        ]);
    }
}
