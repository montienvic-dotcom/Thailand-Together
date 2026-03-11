<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * GET /api/mobile/notifications
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = min((int) $request->input('limit', 20), 100);
        $offset = max((int) $request->input('offset', 0), 0);

        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'data' => $n->data,
                'read_at' => $n->read_at?->toIso8601String(),
                'created_at' => $n->created_at->toIso8601String(),
            ]);

        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'data' => $notifications,
            'meta' => [
                'limit' => $limit,
                'offset' => $offset,
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    /**
     * POST /api/mobile/notifications/{id}/read
     */
    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * POST /api/mobile/notifications/read-all
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * DELETE /api/mobile/notifications/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $request->user()->notifications()->where('id', $id)->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * POST /api/mobile/device-token
     */
    public function registerDeviceToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|max:500',
            'platform' => 'required|string|in:ios,android,web',
            'device_name' => 'sometimes|string|max:255',
        ]);

        DB::table('device_tokens')->updateOrInsert(
            [
                'user_id' => $request->user()->id,
                'token' => $validated['token'],
            ],
            [
                'platform' => $validated['platform'],
                'device_name' => $validated['device_name'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['message' => 'Device token registered']);
    }
}
