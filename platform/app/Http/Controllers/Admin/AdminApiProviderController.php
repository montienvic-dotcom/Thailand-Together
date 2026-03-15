<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Integration\ApiCredential;
use App\Models\Integration\ApiProvider;
use App\Services\ApiGateway\ApiGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminApiProviderController extends Controller
{
    /**
     * Create a new API provider.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:payment,sms,ai,cloud_point,data_exchange,helpdesk,auth',
            'description' => 'nullable|string|max:1000',
            'base_url' => 'nullable|url|max:500',
            'docs_url' => 'nullable|url|max:500',
            'adapter_class' => 'nullable|string|max:255',
            'is_shared' => 'sometimes|boolean',
            'supported_countries' => 'nullable|array',
            'default_config' => 'nullable|array',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = true;

        $provider = ApiProvider::create($data);

        return response()->json([
            'message' => 'API Provider created successfully.',
            'provider' => $provider,
        ], 201);
    }

    /**
     * Update an API provider.
     */
    public function update(Request $request, ApiProvider $provider): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|in:payment,sms,ai,cloud_point,data_exchange,helpdesk,auth',
            'description' => 'nullable|string|max:1000',
            'base_url' => 'nullable|url|max:500',
            'docs_url' => 'nullable|url|max:500',
            'adapter_class' => 'nullable|string|max:255',
            'is_shared' => 'sometimes|boolean',
            'supported_countries' => 'nullable|array',
            'default_config' => 'nullable|array',
        ]);

        $provider->update($data);

        return response()->json([
            'message' => 'API Provider updated successfully.',
            'provider' => $provider->fresh(),
        ]);
    }

    /**
     * Toggle provider active status.
     */
    public function toggleActive(ApiProvider $provider): JsonResponse
    {
        $provider->update(['is_active' => !$provider->is_active]);

        return response()->json([
            'message' => $provider->is_active ? 'Provider activated.' : 'Provider deactivated.',
            'is_active' => $provider->is_active,
        ]);
    }

    /**
     * Delete a provider.
     */
    public function destroy(ApiProvider $provider): JsonResponse
    {
        $provider->delete();

        return response()->json(['message' => 'API Provider deleted.']);
    }

    /**
     * Store credentials for a provider.
     */
    public function storeCredential(Request $request, ApiProvider $provider): JsonResponse
    {
        $data = $request->validate([
            'environment' => 'required|in:production,sandbox',
            'country_id' => 'nullable|exists:countries,id',
            'cluster_id' => 'nullable|exists:clusters,id',
            'credentials' => 'required|array',
            'config' => 'nullable|array',
        ]);

        $data['api_provider_id'] = $provider->id;
        $data['is_active'] = true;

        $credential = ApiCredential::create($data);

        return response()->json([
            'message' => 'Credentials saved successfully.',
            'credential' => $credential->makeHidden('credentials'),
        ], 201);
    }

    /**
     * Update credentials.
     */
    public function updateCredential(Request $request, ApiCredential $credential): JsonResponse
    {
        $data = $request->validate([
            'environment' => 'sometimes|in:production,sandbox',
            'credentials' => 'sometimes|array',
            'config' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $credential->update($data);

        return response()->json([
            'message' => 'Credentials updated.',
            'credential' => $credential->fresh()->makeHidden('credentials'),
        ]);
    }

    /**
     * Delete credentials.
     */
    public function destroyCredential(ApiCredential $credential): JsonResponse
    {
        $credential->delete();

        return response()->json(['message' => 'Credentials deleted.']);
    }

    /**
     * Test connection / health check for a provider.
     */
    public function healthCheck(ApiProvider $provider, ApiGatewayService $gateway): JsonResponse
    {
        try {
            $adapter = $gateway->adapter($provider->slug);
            $healthy = $adapter->healthCheck();

            return response()->json([
                'status' => $healthy ? 'ok' : 'error',
                'message' => $healthy ? 'Connection successful.' : 'Health check failed.',
                'provider' => $provider->name,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Connection failed: ' . $e->getMessage(),
                'provider' => $provider->name,
            ], 422);
        }
    }
}
