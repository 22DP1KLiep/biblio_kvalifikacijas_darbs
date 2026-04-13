<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function show(User $user, Request $request)
{
    $authUser = $request->user();

    $isFollowing = $authUser
        ? $authUser->following()
            ->where('following_id', $user->id)
            ->exists()
        : false;

    // 🔥 Pievienojam publiskās mapes
    $publicFolders = $user->folders()
        ->where('is_public', true)
        ->with('books')
        ->get();

    return Inertia::render('Users/Show', [
        'profileUser' => [
            'id' => $user->id,
            'username' => $user->username,
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
        ],
        'isFollowing' => $isFollowing,
        'publicFolders' => $publicFolders, // 🔥 ŠIS TRŪKA
    ]);
}
public function search(Request $request)
{
    $q = $request->query('q');

    if (!$q) {
        return response()->json([]);
    }

    $users = User::where('username', 'like', "%{$q}%")
        ->orWhere('name', 'like', "%{$q}%")
        ->limit(8)
        ->get(['id', 'name', 'username']);

    return response()->json($users);
}

public function index(Request $request)
{
    $q = $request->query('q');

    $users = User::when($q, function ($query) use ($q) {
        $query->where('username', 'like', "%{$q}%")
              ->orWhere('name', 'like', "%{$q}%");
    })
    ->paginate(15)
    ->withQueryString();

    return Inertia::render('Users/Index', [
        'users' => $users,
        'search' => $q,
    ]);
}

}