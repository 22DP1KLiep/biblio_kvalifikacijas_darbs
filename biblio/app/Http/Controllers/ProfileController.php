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
     * Attēlo lietotāja profila rediģēšanas formu.
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
        // Aizpilda lietotāja datus ar validētajām vērtībām
        $request->user()->fill($request->validated());

        // Ja tiek mainīts e-pasts, noņem e-pasta apstiprinājumu
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Saglabā izmaiņas datubāzē
        $request->user()->save();

        // pāradresē atpakaļ uz profila rediģēšanas lapu
        return Redirect::route('profile.edit');
    }

    /**
     * Dzēš lietotāja kontu.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Pārbauda lietotāja paroli pirms konta dzēšanas
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // iegūst pašreizējo lietotāju
        $user = $request->user();

        // Atslēdz lietotāju no sistēmas
        Auth::logout();

        // Dzēš lietotāja kontu
        $user->delete();

        // Dzēš aktīvo sesiju un ģenerē jaunu CSRF tokenu
        $request->session()->invalidate();

        // ģenerē jaunu CSRF tokenu
        $request->session()->regenerateToken();

        // pāradresē uz sākumlapu
        return Redirect::to('/');
    }

    /**
     * Attēlo izvēlētā lietotāja publisko profilu.
     */
    public function show(User $user)
    {
        // Iegūst pašreiz autorizēto lietotāju
        $authUser = auth()->user();

        // Pievieno sekotāju un sekojamo skaitu
        $user->loadCount(['followers', 'following']);

        // Iegūst tikai publiski pieejamās mapes
        $publicFolders = $user->folders()
            ->where('is_public', true)
            ->with('books')
            ->get();

        return Inertia::render('Profile/Show', [
            'profileUser' => $user,

            // Pārbauda, vai autorizētais lietotājs seko šim profilam
            'isFollowing' => $authUser
                ? $authUser->following()
                    ->where('following_id', $user->id)
                    ->exists()
                : false,

            'publicFolders' => $publicFolders,
        ]);
    }
}