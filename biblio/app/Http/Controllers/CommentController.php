<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($bookId)
    {
        return Comment::with('user')
            ->withCount('likes')
            ->where('book_id', $bookId)
            ->orderByDesc('likes_count')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($comment) {
                $comment->is_liked = auth()->check()
                    ? $comment->likes()->where('user_id', auth()->id())->exists()
                    : false;

                return $comment;
            });
    }

    public function store(Request $request, $bookId)
    {
        // 🔒 JA NAV USER VAI IR RESTRICTED
        if (!auth()->check() || auth()->user()->isRestricted()) {
            return response()->json([
                'message' => 'Tava konta aktivitātes ir ierobežotas līdz ' 
                    . (auth()->user()?->restrictionEndsAt() ?? '')
            ], 403);
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        return Comment::create([
            'book_id' => $bookId,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);
    }

    public function destroy($commentId)
    {
        $user = auth()->user();

        // 🔒 JA RESTRICTED
        if ($user && $user->isRestricted()) {
            abort(403, 'Tu nevari dzēst komentārus, jo esi ierobežots');
        }

        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== $user->id && $user->role !== 'admin') {
            abort(403);
        }

        $comment->delete();

        return redirect()->back();
    }

    public function like($id)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 🔒 JA RESTRICTED
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari balsot par komentāriem, jo esi ierobežots'
            ], 403);
        }

        $comment = Comment::findOrFail($id);

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