<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SSO\SsoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private SsoService $ssoService,
    ) {}

    /**
     * Login with email/password. Returns SSO token.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'sometimes|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->ssoService->authenticateWithCredentials(
            $request->input('email'),
            $request->input('password')
        );

        if (!$user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $this->ssoService->createToken($user, $request->input('device_name', 'api'));
        $clusterId = $request->header('X-Cluster-Id');

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'session' => $this->ssoService->buildSessionPayload($user, $clusterId ? (int) $clusterId : null),
        ]);
    }

    /**
     * Login via SSO provider (Google, Facebook, LINE, etc.)
     */
    public function ssoLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|in:google,facebook,line,apple',
            'provider_id' => 'required|string',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'phone' => 'sometimes|string|max:20',
            'avatar' => 'sometimes|url|max:500',
            'device_name' => 'sometimes|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->ssoService->authenticateWithProvider(
            $request->input('provider'),
            $request->input('provider_id'),
            $request->only(['name', 'email', 'phone', 'avatar'])
        );

        $token = $this->ssoService->createToken($user, $request->input('device_name', 'api'));
        $clusterId = $request->header('X-Cluster-Id');

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'session' => $this->ssoService->buildSessionPayload($user, $clusterId ? (int) $clusterId : null),
        ]);
    }

    /**
     * Get current session info (refresh access map).
     */
    public function session(Request $request): JsonResponse
    {
        $clusterId = $request->header('X-Cluster-Id');

        return response()->json([
            'session' => $this->ssoService->buildSessionPayload(
                $request->user(),
                $clusterId ? (int) $clusterId : null
            ),
        ]);
    }

    /**
     * Logout (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
