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
        }

        return response()->json([
            'likes_count' => $comment->likes()->count(),
            'is_liked' => $isLiked
        ]);
    }
}
