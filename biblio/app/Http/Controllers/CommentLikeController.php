<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function toggle(Comment $comment)
{
    $user = auth()->user();

    $existing = CommentLike::where('comment_id', $comment->id)
        ->where('user_id', $user->id)
        ->first();

    if ($existing) {
        $existing->delete();
    } else {
        CommentLike::create([
            'comment_id' => $comment->id,
            'user_id' => $user->id,
        ]);
    }

    return response()->json(['success' => true]);
}
}
