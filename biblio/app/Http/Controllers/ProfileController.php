<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;    
use Inertia\Inertia;
use App\Models\User;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Attēlo lietotāja profila rediģēšanas lapu.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [

            // pārbauda vai lietotājam nepieciešama e-pasta verifikācija
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,

            // nosūta statusa ziņojumu no sesijas
            'status' => session('status'),
        ]);
    }

    /**
     * Atjaunina lietotāja profila informāciju.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // aizpilda lietotāja datus ar validētajiem datiem
        $request->user()->fill($request->validated());

        // ja ir mainīts e-pasts, noņem verifikāciju
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // saglabā izmaiņas datubāzē
        $request->user()->save();

        // pāradresē atpakaļ uz profila rediģēšanas lapu
        return Redirect::route('profile.edit');
    }

    /**
     * Dzēš lietotāja kontu.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // validē ievadīto paroli
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // iegūst pašreizējo lietotāju
        $user = $request->user();

        // izraksta lietotāju no sistēmas
        Auth::logout();

        // dzēš lietotāja kontu
        $user->delete();

        // invalidē sesiju drošības nolūkos
        $request->session()->invalidate();

        // ģenerē jaunu CSRF tokenu
        $request->session()->regenerateToken();

        // pāradresē uz sākumlapu
        return Redirect::to('/');
    }

    /**
     * Attēlo konkrēta lietotāja publisko profilu.
     */
    public function show(User $user)
    {
        // iegūst autorizēto lietotāju
        $authUser = auth()->user();

        // pievieno sekotāju un sekošanas skaitu
        $user->loadCount(['followers', 'following']);

        // iegūst tikai publiskās mapes ar grāmatām
        $publicFolders = $user->folders()
            ->where('is_public', true)
            ->with('books')
            ->get();

        return Inertia::render('Profile/Show', [

            // nosūta profila lietotāja datus
            'profileUser' => $user,

            // pārbauda vai autorizētais lietotājs seko šim profilam
            'isFollowing' => $authUser
                ? $authUser->following()
                    ->where('following_id', $user->id)
                    ->exists()
                : false,

            // nosūta publiskās mapes
            'publicFolders' => $publicFolders,
        ]);
    }
}