<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
    'username' => [
        'required',
        'string',
        'max:255',
        'regex:/^[A-Za-z0-9]+$/',
        'min:4',
        'unique:users,username,' . $user->id,
    ],
    'email' => [
        'required',
        'email',
        'max:255',
        'unique:users,email,' . $user->id,
    ],
    'password' => [
        'nullable',
        'min:6',
        'confirmed',
        'regex:/[A-Z]/',
        'regex:/[0-9]/',
    ],
], [
    // USERNAME
    'username.required' => 'Ievadi lietotājvārdu',
    'username.min' => 'Vismaz 4 simboli',
    'username.regex' => 'Tikai burti un cipari',
    'username.unique' => 'Šis lietotājvārds jau ir aizņemts',

    // EMAIL
    'email.required' => 'Ievadi e-pastu',
    'email.email' => 'Nepareizs e-pasts',
    'email.unique' => 'Šis e-pasts jau tiek izmantots',

    // PASSWORD
    'password.min' => 'Vismaz 6 simboli',
    'password.confirmed' => 'Paroles nesakrīt',
    'password.regex' => 'Jābūt lielajam burtam un ciparam',
]);

    $user->username = $request->username;
    $user->email = $request->email;

    if ($request->password) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return response()->json([
        'message' => 'Saglabāts!',
        'user' => [
            'username' => $user->username,
            'email' => $user->email,
        ]
    ]);
}

}