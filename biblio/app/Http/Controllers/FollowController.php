<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;


class FollowController extends Controller
{
    public function toggle(User $user, Request $request)
{
    $authUser = $request->user();

    if ($authUser->id === $user->id) {
        return back();
    }

    if ($authUser->following()->where('following_id', $user->id)->exists()) {
        $authUser->following()->detach($user->id);
    } else {
        $authUser->following()->attach($user->id);
    }

    return back();

    Notification::create([
    'user_id' => $followedUser->id,
    'from_user_id' => auth()->id(),
    'type' => 'follow'
]);
}
}
