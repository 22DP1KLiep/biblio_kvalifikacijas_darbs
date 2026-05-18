<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Inertia\Inertia;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::where('user_id', $user->id)
            ->with('fromUser:id,username')
            ->latest()
            ->get();

        //  atzīmē visas kā izlasītas
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications
        ]);
    }
}