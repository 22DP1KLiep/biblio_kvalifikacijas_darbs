<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function toggle($id)
    {
        // atrodam komentāru pēc id
        $comment = Comment::findOrFail($id);

        // iegūstam autorizēto lietotāju
        $user = auth()->user();

        // pārbaudām vai lietotājs ir pieslēdzies
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // pārbaudām vai lietotājs jau ir ielicis like šim komentāram
        $existing = $comment->likes()->where('user_id', $user->id)->first();

        // ja like jau eksistē — noņemam to
        if ($existing) {

            // dzēšam like ierakstu
            $existing->delete();

            // saglabājam statusu, ka komentārs vairs nav ielaikots
            $isLiked = false;

        } else {

            // izveidojam jaunu like ierakstu
            $comment->likes()->create([
                'user_id' => $user->id,
                'comment_id' => $comment->id
            ]);

            // saglabājam statusu, ka komentārs ir ielaikots
            $isLiked = true;

            // izveidojam notifikāciju tikai tad,
            // ja lietotājs nelaiko pats savu komentāru
            if ($comment->user_id !== $user->id) {

                \App\Models\Notification::create([

                    // kam tiks nosūtīta notifikācija
                    'user_id' => $comment->user_id,

                    // kurš veica darbību
                    'from_user_id' => $user->id,

                    // notifikācijas tips
                    'type' => 'comment_like',

                    // papildu dati par komentāru un grāmatu
                    'data' => [
                        'comment_id' => $comment->id,
                        'book_id' => $comment->book_id
                    ]
                ]);
            }
        }

        // atgriežam jauno like skaitu un statusu
        return response()->json([
            'likes_count' => $comment->likes()->count(),
            'is_liked' => $isLiked
        ]);
    }
}