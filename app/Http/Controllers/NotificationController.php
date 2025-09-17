<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth; // For JWT auth

class NotificationController extends Controller
{
    // Get all notifications for logged-in user
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $notifications = Notification::where('user_id', $user->id)
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        return response()->json($notifications);
    }

    // Mark as read
    public function markAsRead($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $notification = Notification::where('id', $id)
                                    ->where('user_id', $user->id)
                                    ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Marked as read']);
    }

    // Create a notification (can be used anywhere)
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'message' => $request->message,
        ]);

        return response()->json($notification, 201);
    }
}