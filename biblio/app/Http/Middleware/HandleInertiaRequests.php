<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Notification;

class HandleInertiaRequests extends Middleware
{
    /**
     * Galvenais Blade skats, kas tiek ielādēts pirmajā lapas apmeklējumā.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Nosaka pašreizējo resursu versiju.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Definē datus, kas tiek koplietoti visās Inertia lapās.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [

            // Autorizētā lietotāja dati
            'auth' => [
                'user' => $request->user()
                    ? [
                        'id' => $request->user()->id,
                        'username' => $request->user()->username,
                        'email' => $request->user()->email,
                        'role' => $request->user()->role,
                        'avatar' => $request->user()->avatar,

                        // Sekotāju un sekojamo statistika
                        'followers_count' => $request->user()->followers()->count(),
                        'following_count' => $request->user()->following()->count(),

                        // Lietotāja ierobežojumu informācija
                        'restricted' => $request->user()->isRestricted(),
                        'restricted_until' => $request->user()->restrictionEndsAt(),
                        'restriction_reason' => $request->user()->restriction_reason,
                    ]
                    : null,
            ],

            // Neizlasīto paziņojumu skaits
            'notificationsCount' => function () use ($request) {

                if (!$request->user()) {
                    return 0;
                }

                return Notification::where('user_id', $request->user()->id)
                    ->where('is_read', false)
                    ->count();
            },

            // Pēdējie 5 paziņojumi navigācijas izvēlnei
            'latestNotifications' => function () use ($request) {

                if (!$request->user()) {
                    return [];
                }

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

                            // Lietotājs, kurš izveidoja paziņojumu
                            'from_username' => $n->fromUser?->username,

                            // Papilddati, kas saistīti ar paziņojumu
                            'conversation_id' => $n->data,
                        ];
                    });
            },
        ]);
    }
}