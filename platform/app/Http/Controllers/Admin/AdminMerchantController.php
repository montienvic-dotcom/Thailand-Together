<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMerchantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('merchant');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tier')) {
            $query->where('default_tier_code', $request->input('tier'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_th', 'like', "%{$search}%")
                  ->orWhere('merchant_code', 'like', "%{$search}%");
            });
        }

        $merchants = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'message' => 'Merchants retrieved successfully.',
            'merchants' => $merchants,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'merchant_code' => 'required|string|max:255|unique:merchant,merchant_code',
            'name_en' => 'required|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'default_tier_code' => 'nullable|string|max:50',
            'service_tags' => 'nullable|array',
            'status' => 'required|in:active,inactive,pending',
            'cluster_id' => 'required|exists:clusters,id',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|email|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        if (isset($data['service_tags'])) {
            $data['service_tags'] = json_encode($data['service_tags']);
        }

        $now = now();
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        DB::table('merchant')->insert($data);

        $merchant = DB::table('merchant')
            ->where('merchant_code', $data['merchant_code'])
            ->first();

        return response()->json([
            'message' => 'Merchant created successfully.',
            'merchant' => $merchant,
        ], 201);
    }

    public function update(Request $request, string $merchantCode): JsonResponse
    {
        $merchant = DB::table('merchant')->where('merchant_code', $merchantCode)->first();

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found.'], 404);
        }

        $data = $request->validate([
            'name_en' => 'sometimes|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'default_tier_code' => 'nullable|string|max:50',
            'service_tags' => 'nullable|array',
            'status' => 'sometimes|in:active,inactive,pending',
            'cluster_id' => 'sometimes|exists:clusters,id',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|email|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        if (isset($data['service_tags'])) {
            $data['service_tags'] = json_encode($data['service_tags']);
        }

        $data['updated_at'] = now();

        DB::table('merchant')->where('merchant_code', $merchantCode)->update($data);

        $merchant = DB::table('merchant')->where('merchant_code', $merchantCode)->first();

        return response()->json([
            'message' => 'Merchant updated successfully.',
            'merchant' => $merchant,
        ]);
    }

    public function toggleStatus(string $merchantCode): JsonResponse
    {
        $merchant = DB::table('merchant')->where('merchant_code', $merchantCode)->first();

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found.'], 404);
        }

        $newStatus = $merchant->status === 'active' ? 'inactive' : 'active';

        DB::table('merchant')->where('merchant_code', $merchantCode)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => $newStatus === 'active' ? 'Merchant activated.' : 'Merchant deactivated.',
            'status' => $newStatus,
        ]);
    }

    public function destroy(string $merchantCode): JsonResponse
    {
        $merchant = DB::table('merchant')->where('merchant_code', $merchantCode)->first();

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found.'], 404);
        }

        DB::table('merchant')->where('merchant_code', $merchantCode)->delete();

        return response()->json(['message' => 'Merchant deleted successfully.']);
    }

    public function stats(): JsonResponse
    {
        $byStatus = DB::table('merchant')
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $byTier = DB::table('merchant')
            ->selectRaw('default_tier_code, count(*) as count')
            ->groupBy('default_tier_code')
            ->pluck('count', 'default_tier_code');

        return response()->json([
            'message' => 'Merchant stats retrieved successfully.',
            'stats' => [
                'by_status' => $byStatus,
                'by_tier' => $byTier,
                'total' => DB::table('merchant')->count(),
            ],
        ]);
    }
}
