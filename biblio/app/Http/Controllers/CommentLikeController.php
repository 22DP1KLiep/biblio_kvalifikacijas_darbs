<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function toggle($id)
    {
        $comment = Comment::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $existing = $comment->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $isLiked = false;
        } else {
    $comment->likes()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id
    ]);
    $isLiked = true;

    // 🔔 NOTIFICATION
    if ($comment->user_id !== $user->id) {
        \App\Models\Notification::create([
            'user_id' => $comment->user_id,
            'from_user_id' => $user->id,
            'type' => 'comment_like',
            'data' => [
                'comment_id' => $comment->id,
                'book_id' => $comment->book_id
            ]
        ]);
    }
}

        return response()->json([
            'likes_count' => $comment->likes()->count(),
            'is_liked' => $isLiked
        ]);
    }
}
