<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Parāda konkrēta lietotāja profilu
    public function show(User $user, Request $request)
    {
        // Pašreiz autorizētais lietotājs
        $authUser = $request->user();

        // Pārbauda vai autorizētais lietotājs seko šim profilam
        $isFollowing = $authUser
            ? $authUser->following()
                ->where('following_id', $user->id)
                ->exists()
            : false;

        // Ielādē tikai publiskās mapes kopā ar grāmatām
        $publicFolders = $user->folders()
            ->where('is_public', true)
            ->with('books')
            ->get();

        // Atgriež lietotāja profila lapu ar nepieciešamajiem datiem
        return Inertia::render('Users/Show', [
            'profileUser' => [
                'id' => $user->id,
                'username' => $user->username,
                'avatar' => $user->avatar,

                // Sekotāju skaits
                'followers_count' => $user->followers()->count(),

                // Cik profiliem lietotājs seko
                'following_count' => $user->following()->count(),
            ],

            // Vai pašreizējais lietotājs seko šim profilam
            'isFollowing' => $isFollowing,

            // Publiskās mapes
            'publicFolders' => $publicFolders,
        ]);
    }

    // Meklē lietotājus pēc username
    public function search(Request $request)
    {
        // Iegūst meklēšanas tekstu no URL
        $q = $request->query('q');

        // Ja nekas nav ievadīts — atgriež tukšu masīvu
        if (!$q) {
            return response()->json([]);
        }

        // Meklē lietotājus pēc username
        $users = User::where('username', 'like', "%{$q}%")
            ->limit(8)
            ->get([
                'id',
                'username',
                'email'
            ]);

        // Atgriež rezultātus JSON formātā
        return response()->json($users);
    }

    // Parāda visu lietotāju sarakstu
    public function index(Request $request)
    {
        // Meklēšanas teksts
        $q = $request->query('q');

        // Meklē lietotājus pēc username vai name
        $users = User::when($q, function ($query) use ($q) {
            $query->where('username', 'like', "%{$q}%")
                  ->orWhere('name', 'like', "%{$q}%");
        })
        ->paginate(15)
        ->withQueryString();

        // Atgriež lietotāju saraksta lapu
        return Inertia::render('Users/Index', [
            'users' => $users,
            'search' => $q,
        ]);
    }

    // Atjauno lietotāja profila informāciju
    public function update(Request $request)
    {
        // Pašreiz autorizētais lietotājs
        $user = Auth::user();

        // Validācija
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

            // Lietotājvārda kļūdas
            'username.required' => 'Ievadi lietotājvārdu',
            'username.min' => 'Vismaz 4 simboli',
            'username.regex' => 'Tikai burti un cipari',
            'username.unique' => 'Šis lietotājvārds jau ir aizņemts',

            // E-pasta kļūdas
            'email.required' => 'Ievadi e-pastu',
            'email.email' => 'Nepareizs e-pasts',
            'email.unique' => 'Šis e-pasts jau tiek izmantots',

            // Paroles kļūdas
            'password.min' => 'Vismaz 6 simboli',
            'password.confirmed' => 'Paroles nesakrīt',
            'password.regex' => 'Jābūt lielajam burtam un ciparam',
        ]);

        // Saglabā jauno username
        $user->username = $request->username;

        // Saglabā jauno e-pastu
        $user->email = $request->email;

        // Ja ievadīta parole — sašifrē un saglabā
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        // Saglabā izmaiņas datubāzē
        $user->save();

        // Atgriež veiksmīgu atbildi
        return response()->json([
            'message' => 'Saglabāts!',
            'user' => [
                'username' => $user->username,
                'email' => $user->email,
            ]
        ]);
    }

    // Augšupielādē lietotāja profila attēlu
    public function uploadAvatar(Request $request)
    {
        // Pārbauda vai augšupielādēts attēls
        $request->validate([
            'avatar' => 'required|image|max:5120'
        ]);

        // Saglabā attēlu storage/public/avatars mapē
        $path = $request->file('avatar')->store('avatars', 'public');

        // Iegūst autorizēto lietotāju
        $user = Auth::user();

        // Saglabā avatar ceļu datubāzē
        $user->avatar = $path;

        // Saglabā izmaiņas
        $user->save();

        // Atgriež avatar ceļu
        return response()->json([
            'avatar' => $path
        ]);
    }
}