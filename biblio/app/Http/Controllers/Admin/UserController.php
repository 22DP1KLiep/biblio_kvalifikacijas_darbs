<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Notification;

class UserController extends Controller
{
    // Attēlo visu lietotāju sarakstu administratora panelī
    public function index()
    {
        return Inertia::render('Admin/Users', [
            'users' => User::select(
                'id',
                'name',
                'email',
                'role',
                'restricted_until',
                'restriction_reason'
            )->get(),
        ]);
    }

    // Maina lietotāja lomu (admin vai user)
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        // Neļauj administratoram mainīt savu lomu
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'role' => 'You cannot change your own role.',
            ]);
        }

        $user->update([
            'role' => $request->role,
        ]);

        return back();
    }

    // Dzēš izvēlēto lietotāju
    public function destroy(User $user)
    {
        // Neļauj administratoram izdzēst pašam sevi
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'user' => 'You cannot delete yourself.',
            ]);
        }

        $user->delete();

        return back();
    }

    // Uzliek lietotājam pagaidu ierobežojumu
    public function restrict(Request $request, User $user)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        // Aprēķina ierobežojuma beigu datumu
        $until = now()->addDays($request->days);

        // Saglabā ierobežojuma informāciju lietotāja profilā
        $user->update([
            'restricted_until' => $until,
            'restriction_reason' => $request->reason,
        ]);

        // Izveido paziņojumu lietotājam par saņemto ierobežojumu
        Notification::create([
            'user_id' => $user->id,
            'from_user_id' => auth()->id(),
            'type' => 'restriction',
            'data' => json_encode([
                'reason' => $request->reason,
                'until' => $until,
            ]),
            'is_read' => false,
        ]);

        return back();
    }

    // Noņem lietotājam iepriekš uzlikto ierobežojumu
    public function removeRestriction(User $user)
    {
        $user->update([
            'restricted_until' => null,
            'restriction_reason' => null,
        ]);

        // Izveido paziņojumu par ierobežojuma noņemšanu
        Notification::create([
            'user_id' => $user->id,
            'from_user_id' => auth()->id(),
            'type' => 'restriction_removed',
            'data' => null,
            'is_read' => false,
        ]);

        return back();
    }
}