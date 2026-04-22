<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->with(['actor', 'board', 'card'])
            ->latest('created_at')
            ->take(30)
            ->get();

        return response()->json([
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $request->user()
                ->notifications()
                ->where('is_read', false)
                ->count(),
        ]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => "Notification '{$notification->type}' has been marked as read.",
            'notification_id' => $notification->id,
        ]);
    }

    public function markAllRead(Request $request)
    {
        $count = $request->user()
            ->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => "{$count} notification(s) marked as read.",
            'updated_count' => $count
        ]);
    }

    public function markunRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $notification->update(['is_read' => false]);

        return response()->json([
            'success' => true,
            'message' => "Notification '{$notification->type}' has been marked as unread.",
            'notification_id' => $notification->id,
        ]);
    }

}
