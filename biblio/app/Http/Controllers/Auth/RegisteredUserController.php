<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;


class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('AuthForm'); // ja tu visu dari vienā Vue komponentē
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'min:4',
                'max:255',
                'regex:/^[A-Za-z0-9]+$/', // Tikai burti un cipari
                'not_regex:/^\d+$/',      // Ne tikai cipari
                'unique:users,username'
            ],
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(), // vismaz 8 simboli, burti, cipari u.c.
            ],
        ],
            [
                'username.regex' => 'Lietotājvārds drīkst saturēt tikai burtus un ciparus.',
                'username.not_regex' => 'Lietotājvārds nedrīkst būt tikai cipari.',
            ]);

        $user = User::create([
            'name' => $request->username,  // Laravel joprojām izmanto 'name'
            'username' => $request->username, // mūsu papildu lauks
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('home'); // vai / vai cita lapas rinda
    }
}
