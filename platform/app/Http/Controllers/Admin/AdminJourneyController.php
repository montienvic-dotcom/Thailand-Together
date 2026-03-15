<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminJourneyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('journey');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('journey_code', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('title_th', 'like', "%{$search}%")
                  ->orWhere('journey_group', 'like', "%{$search}%");
            });
        }

        $journeys = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'message' => 'Journeys retrieved successfully.',
            'journeys' => $journeys,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'journey_code' => 'required|string|max:255|unique:journey,journey_code',
            'title_en' => 'required|string|max:255',
            'title_th' => 'nullable|string|max:255',
            'journey_group' => 'required|string|max:255',
            'cluster_id' => 'required|exists:clusters,id',
            'status' => ['required', Rule::in(['draft', 'active', 'archived'])],
            'total_minutes_sum' => 'nullable|integer|min:0',
            'gmv_per_person' => 'nullable|numeric|min:0',
            'luxury_tone_en' => 'nullable|string|max:255',
            'group_size' => 'nullable|integer|min:1',
            'tp_total_normal' => 'nullable|integer|min:0',
            'tp_total_goal' => 'nullable|integer|min:0',
            'tp_total_special' => 'nullable|integer|min:0',
        ]);

        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('journey')->insert($data);

        $journey = DB::table('journey')->where('journey_code', $data['journey_code'])->first();

        return response()->json([
            'message' => 'Journey created successfully.',
            'journey' => $journey,
        ], 201);
    }

    public function update(Request $request, string $journeyCode): JsonResponse
    {
        $journey = DB::table('journey')->where('journey_code', $journeyCode)->first();

        if (!$journey) {
            return response()->json(['message' => 'Journey not found.'], 404);
        }

        $data = $request->validate([
            'title_en' => 'sometimes|string|max:255',
            'title_th' => 'nullable|string|max:255',
            'journey_group' => 'sometimes|string|max:255',
            'cluster_id' => 'sometimes|exists:clusters,id',
            'status' => ['sometimes', Rule::in(['draft', 'active', 'archived'])],
            'total_minutes_sum' => 'nullable|integer|min:0',
            'gmv_per_person' => 'nullable|numeric|min:0',
            'luxury_tone_en' => 'nullable|string|max:255',
            'group_size' => 'nullable|integer|min:1',
            'tp_total_normal' => 'nullable|integer|min:0',
            'tp_total_goal' => 'nullable|integer|min:0',
            'tp_total_special' => 'nullable|integer|min:0',
        ]);

        $data['updated_at'] = now();

        DB::table('journey')->where('journey_code', $journeyCode)->update($data);

        $journey = DB::table('journey')->where('journey_code', $journeyCode)->first();

        return response()->json([
            'message' => 'Journey updated successfully.',
            'journey' => $journey,
        ]);
    }

    public function toggleStatus(string $journeyCode): JsonResponse
    {
        $journey = DB::table('journey')->where('journey_code', $journeyCode)->first();

        if (!$journey) {
            return response()->json(['message' => 'Journey not found.'], 404);
        }

        $newStatus = $journey->status === 'active' ? 'archived' : 'active';

        DB::table('journey')->where('journey_code', $journeyCode)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => $newStatus === 'active' ? 'Journey activated.' : 'Journey archived.',
            'status' => $newStatus,
        ]);
    }

    public function destroy(string $journeyCode): JsonResponse
    {
        $journey = DB::table('journey')->where('journey_code', $journeyCode)->first();

        if (!$journey) {
            return response()->json(['message' => 'Journey not found.'], 404);
        }

        DB::table('journey')->where('journey_code', $journeyCode)->delete();

        return response()->json(['message' => 'Journey deleted successfully.']);
    }
}
