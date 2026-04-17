<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Notification;


class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
{
    return array_merge(parent::share($request), [
        'auth' => [
            'user' => $request->user()
                ? [
                    'id' => $request->user()->id,
                    'username' => $request->user()->username,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'followers_count' => $request->user()->followers()->count(),
                    'following_count' => $request->user()->following()->count(),
                ]
                : null,
        ],

        // 🔔 Unread count
        'notificationsCount' => function () use ($request) {
            if (!$request->user()) return 0;

            return Notification::where('user_id', $request->user()->id)
                ->where('is_read', false)
                ->count();
        },

        // 🔔 Dropdown pēdējās 5 notifikācijas
        'latestNotifications' => function () use ($request) {
            if (!$request->user()) return [];

            return Notification::where('user_id', $request->user()->id)
                ->with('fromUser:id,username')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($n) {
                    return [
                        'id' => $n->id,
                        'type' => $n->type,
                        'is_read' => $n->is_read,
                        'from_username' => $n->fromUser?->username,
                        'conversation_id' => $n->data,
                    ];
                });
        },
    ]);
}
}
