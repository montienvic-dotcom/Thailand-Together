<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * GET /api/mobile/bookings
     * List authenticated user's bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = min((int) $request->input('limit', 20), 100);
        $offset = max((int) $request->input('offset', 0), 0);
        $status = $request->input('status'); // upcoming, completed, cancelled

        $query = DB::table('bookings')
            ->where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $total = (clone $query)->count();

        $bookings = $query
            ->orderByDesc('booking_date')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'data' => $bookings,
            'meta' => ['total' => $total, 'limit' => $limit, 'offset' => $offset],
        ]);
    }

    /**
     * GET /api/mobile/bookings/{id}
     * Booking detail.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        // Get booking items
        $items = DB::table('booking_items')
            ->where('booking_id', $id)
            ->get();

        return response()->json([
            'data' => [
                'booking' => $booking,
                'items' => $items,
            ],
        ]);
    }

    /**
     * POST /api/mobile/bookings
     * Create a new booking.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'merchant_id' => 'required|integer',
            'journey_code' => 'sometimes|nullable|string|max:50',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'sometimes|nullable|date_format:H:i',
            'party_size' => 'sometimes|integer|min:1|max:99',
            'notes' => 'sometimes|nullable|string|max:1000',
            'items' => 'sometimes|array',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ]);

        $user = $request->user();
        $bookingCode = 'BK-' . strtoupper(Str::random(8));

        $totalAmount = 0;
        if (! empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }
        }

        $bookingId = DB::table('bookings')->insertGetId([
            'booking_code' => $bookingCode,
            'user_id' => $user->id,
            'merchant_id' => $validated['merchant_id'],
            'journey_code' => $validated['journey_code'] ?? null,
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'] ?? null,
            'party_size' => $validated['party_size'] ?? 1,
            'total_amount' => $totalAmount,
            'status' => 'upcoming',
            'notes' => $validated['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! empty($validated['items'])) {
            $itemRows = [];
            foreach ($validated['items'] as $item) {
                $itemRows[] = [
                    'booking_id' => $bookingId,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'created_at' => now(),
                ];
            }
            DB::table('booking_items')->insert($itemRows);
        }

        $booking = DB::table('bookings')->where('id', $bookingId)->first();

        return response()->json([
            'data' => $booking,
            'message' => 'Booking created successfully',
        ], 201);
    }

    /**
     * PUT /api/mobile/bookings/{id}/cancel
     * Cancel a booking.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['error' => 'Booking is already cancelled'], 422);
        }

        if ($booking->status === 'completed') {
            return response()->json(['error' => 'Cannot cancel a completed booking'], 422);
        }

        DB::table('bookings')
            ->where('id', $id)
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->input('reason'),
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Booking cancelled']);
    }
}
