<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Services\SSO\SsoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private SsoService $ssoService,
    ) {}

    /**
     * GET /api/mobile/profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'locale' => $user->locale,
                'sso_provider' => $user->sso_provider,
                'last_login_at' => $user->last_login_at?->toIso8601String(),
                'created_at' => $user->created_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * PUT /api/mobile/profile
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|url|max:500',
            'locale' => 'sometimes|string|in:th,en,zh,ja,ko,ru',
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'locale' => $user->locale,
            ],
            'message' => 'Profile updated',
        ]);
    }

    /**
     * GET /api/mobile/profile/settings
     */
    public function settings(Request $request): JsonResponse
    {
        $user = $request->user();
        $clusterId = $request->header('X-Cluster-Id');

        $payload = $this->ssoService->buildSessionPayload(
            $user,
            $clusterId ? (int) $clusterId : null
        );

        return response()->json([
            'data' => [
                'user' => $payload['user'],
                'locale' => $user->locale,
                'is_global_admin' => $payload['is_global_admin'],
                'roles' => $payload['roles'],
                'groups' => $payload['groups'],
                'cluster' => $payload['cluster'] ?? null,
            ],
        ]);
    }
}
