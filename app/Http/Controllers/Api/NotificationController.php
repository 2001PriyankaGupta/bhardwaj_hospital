<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $user = auth('api')->user();

        $notification = Notification::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $notification->markRead();

        return response()->json(['success' => true, 'message' => 'Notification marked read']);
    }

    public function markAllRead(Request $request)
    {
        $user = auth('api')->user();

        Notification::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'All notifications marked read']);
    }

    public function unreadCount()
    {
        $user = auth('api')->user();
        if (!$user) return response()->json(['unread_count' => 0]);

        $count = Notification::where('user_id', $user->id)->whereNull('read_at')->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }
}
