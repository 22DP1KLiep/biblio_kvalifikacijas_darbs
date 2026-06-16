<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    // Pievieno vai noņem "patīk" reakciju komentāram
    public function toggle($id)
    {
        // Atrod komentāru pēc ID
        $comment = Comment::findOrFail($id);

        // Iegūst autorizēto lietotāju
        $user = auth()->user();

        // Pārbauda, vai lietotājs ir pieslēdzies sistēmai
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Pārbauda, vai lietotājs jau ir atzīmējis komentāru ar "patīk"
        $existing = $comment->likes()
            ->where('user_id', $user->id)
            ->first();

        // ja like jau eksistē — noņemam to
        if ($existing) {

            // Ja reakcija jau eksistē, tā tiek noņemta
            $existing->delete();

            // saglabājam statusu, ka komentārs vairs nav ielaikots
            $isLiked = false;

        } else {

            // Ja reakcijas nav, tā tiek pievienota
            $comment->likes()->create([
                'user_id' => $user->id,
                'comment_id' => $comment->id
            ]);

            $isLiked = true;

            // Izveido paziņojumu komentāra autoram
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

        // Atgriež aktuālo "patīk" skaitu un lietotāja reakcijas statusu
        return response()->json([
            'likes_count' => $comment->likes()->count(),
            'is_liked' => $isLiked
        ]);
    }
}