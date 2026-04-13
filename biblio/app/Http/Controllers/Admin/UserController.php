<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends Controller
{
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

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

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

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'user' => 'You cannot delete yourself.',
            ]);
        }

        $user->delete();

        return back();
    }

    public function restrict(Request $request, User $user)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $user->update([
            'restricted_until' => now()->addDays($request->days),
            'restriction_reason' => $request->reason,
        ]);

        return back();
    }

    public function removeRestriction(User $user)
    {
        $user->update([
            'restricted_until' => null,
            'restriction_reason' => null,
        ]);

        return back();
    }
}