<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminClusterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'code' => 'nullable|string|max:50|unique:clusters,code',
            'description' => 'nullable|string|max:1000',
            'timezone' => 'nullable|string|max:100',
            'default_locale' => 'nullable|string|max:10',
            'database_connection' => 'nullable|string|max:100',
            'launch_date' => 'nullable|date',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['code'] = $data['code'] ?? Str::upper(Str::slug($data['name'], '_'));
        $data['is_active'] = true;
        $data['sort_order'] = (Cluster::max('sort_order') ?? 0) + 1;

        $cluster = Cluster::create($data);

        return response()->json([
            'message' => 'Cluster created successfully.',
            'cluster' => $cluster->load('country'),
        ], 201);
    }

    public function update(Request $request, Cluster $cluster): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'timezone' => 'nullable|string|max:100',
            'default_locale' => 'nullable|string|max:10',
            'database_connection' => 'nullable|string|max:100',
            'launch_date' => 'nullable|date',
        ]);

        $cluster->update($data);

        return response()->json([
            'message' => 'Cluster updated successfully.',
            'cluster' => $cluster->fresh()->load('country'),
        ]);
    }

    public function toggleActive(Cluster $cluster): JsonResponse
    {
        $cluster->update(['is_active' => !$cluster->is_active]);

        return response()->json([
            'message' => $cluster->is_active ? 'Cluster activated.' : 'Cluster deactivated.',
            'is_active' => $cluster->is_active,
        ]);
    }

    public function destroy(Cluster $cluster): JsonResponse
    {
        $cluster->delete();

        return response()->json(['message' => 'Cluster deleted successfully.']);
    }

    /**
     * Sync applications assigned to a cluster.
     */
    public function syncApplications(Request $request, Cluster $cluster): JsonResponse
    {
        $data = $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
        ]);

        $syncData = [];
        foreach ($data['application_ids'] as $appId) {
            $syncData[$appId] = ['is_active' => true];
        }

        $cluster->applications()->sync($syncData);

        return response()->json([
            'message' => 'Applications updated for cluster.',
            'count' => count($data['application_ids']),
        ]);
    }

    /**
     * Toggle a specific app's active status within a cluster.
     */
    public function toggleClusterApp(Request $request, Cluster $cluster, int $applicationId): JsonResponse
    {
        $pivot = $cluster->applications()->where('application_id', $applicationId)->first();

        if (!$pivot) {
            return response()->json(['message' => 'Application not assigned to this cluster.'], 404);
        }

        $newStatus = !$pivot->pivot->is_active;
        $cluster->applications()->updateExistingPivot($applicationId, ['is_active' => $newStatus]);

        return response()->json([
            'message' => $newStatus ? 'Application activated in cluster.' : 'Application deactivated in cluster.',
            'is_active' => $newStatus,
        ]);
    }

    public function storeCountry(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10|unique:countries,code',
            'code_alpha2' => 'nullable|string|max:2',
            'currency_code' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'default_locale' => 'nullable|string|max:10',
        ]);

        $data['code'] = $data['code'] ?? Str::upper(Str::slug($data['name'], '_'));
        $data['is_active'] = true;
        $data['sort_order'] = (Country::max('sort_order') ?? 0) + 1;

        $country = Country::create($data);

        return response()->json([
            'message' => 'Country created successfully.',
            'country' => $country,
        ], 201);
    }

    public function updateCountry(Request $request, Country $country): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'currency_code' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'default_locale' => 'nullable|string|max:10',
        ]);

        $country->update($data);

        return response()->json([
            'message' => 'Country updated successfully.',
            'country' => $country->fresh(),
        ]);
    }

    public function toggleCountry(Country $country): JsonResponse
    {
        $country->update(['is_active' => !$country->is_active]);

        return response()->json([
            'message' => $country->is_active ? 'Country activated.' : 'Country deactivated.',
            'is_active' => $country->is_active,
        ]);
    }
}
