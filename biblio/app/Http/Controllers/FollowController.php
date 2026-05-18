<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;

class FollowController extends Controller
{
    /**
     * Pievieno vai noņem sekošanu lietotājam.
     * 
     * Funkcionalitāte:
     * - Ja lietotājs jau seko → pārtrauc sekošanu
     * - Ja neseko → sāk sekošanu
     * - Izveido paziņojumu jaunajam sekotajam
     */
    public function toggle(User $user, Request $request)
    {
        $authUser = $request->user();

        // Neļauj sekot pašam sev
        if ($authUser->id === $user->id) {
            return back();
        }

        // Pārbauda, vai jau seko
        $isFollowing = $authUser
            ->following()
            ->where('following_id', $user->id)
            ->exists();

        if ($isFollowing) {
            // Noņem sekošanu
            $authUser->following()->detach($user->id);
        } else {
            // Pievieno sekošanu
            $authUser->following()->attach($user->id);

            // Izveido paziņojumu lietotājam, kuram sāk sekot
            Notification::create([
                'user_id' => $user->id,
                'from_user_id' => $authUser->id,
                'type' => 'follow'
            ]);
        }

        return back();
    }
}