<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\Group;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Create a new user.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'status' => 'sometimes|in:active,inactive,suspended',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
            'role_id' => 'nullable|exists:roles,id',
            'cluster_id' => 'nullable|exists:clusters,id',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);

            if (!empty($data['group_ids'])) {
                $user->groups()->sync($data['group_ids']);
            }

            if (!empty($data['role_id'])) {
                DB::table('role_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $data['role_id'],
                    'cluster_id' => $data['cluster_id'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $user;
        });

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user->load('groups', 'roles'),
        ], 201);
    }

    /**
     * Update user details.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'status' => 'sometimes|in:active,inactive,suspended',
            'password' => 'nullable|string|min:8',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Update user's group assignments.
     */
    public function updateGroups(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,id',
        ]);

        $user->groups()->sync($data['group_ids']);

        return response()->json([
            'message' => 'User groups updated.',
            'groups' => $user->fresh()->groups,
        ]);
    }

    /**
     * Update user's role assignment.
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'country_id' => 'nullable|exists:countries,id',
            'cluster_id' => 'nullable|exists:clusters,id',
        ]);

        // Remove existing roles at same scope
        DB::table('role_user')
            ->where('user_id', $user->id)
            ->where('cluster_id', $data['cluster_id'] ?? null)
            ->delete();

        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $data['role_id'],
            'country_id' => $data['country_id'] ?? null,
            'cluster_id' => $data['cluster_id'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'User role updated.',
            'roles' => $user->fresh()->roles,
        ]);
    }

    /**
     * Soft delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->update(['status' => 'inactive']);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    /**
     * Get user detail with groups and roles (JSON).
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user' => $user->load('groups', 'roles'),
        ]);
    }
}
