<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Atgriež visus konkrētās grāmatas komentārus.
     * 
     * Funkcionalitāte:
     * - Ielādē komentārus ar lietotāja datiem
     * - Saskaita reakciju (like) skaitu
     * - Sakārto pēc popularitātes un datuma
     * - Nosaka, vai pašreizējais lietotājs ir atzīmējis komentāru ar "patīk"
     */
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

    /**
     * Pievieno jaunu komentāru konkrētai grāmatai.
     * 
     * Funkcionalitāte:
     * - Pārbauda, vai lietotājs ir autorizēts
     * - Pārbauda, vai lietotājs nav ierobežots
     * - Validē ievadītos datus
     * - Saglabā komentāru datubāzē
     */
    public function store(Request $request, $bookId)
    {
        // Pārbauda, vai lietotājs ir autorizēts un nav ierobežots
        if (!auth()->check() || auth()->user()->isRestricted()) {
            return response()->json([
                'message' => 'Tava konta aktivitātes ir ierobežotas līdz ' 
                    . (auth()->user()?->restrictionEndsAt() ?? '')
            ], 403);
        }

        // Datu validācija
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Komentāra saglabāšana
        return Comment::create([
            'book_id' => $bookId,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);
    }

    /**
     * Dzēš komentāru.
     * 
     * Funkcionalitāte:
     * - Pārbauda, vai lietotājs nav ierobežots
     * - Atļauj dzēst tikai komentāra autoram vai administratoram
     * - Dzēš komentāru no datubāzes
     */
    public function destroy($commentId)
{
    $user = auth()->user();

    if ($user && $user->isRestricted()) {
        abort(403);
    }

    $comment = Comment::findOrFail($commentId);

    if ($comment->user_id !== $user->id && $user->role !== 'admin') {
        abort(403);
    }

    $comment->delete();

    return response()->json([
        'success' => true
    ]);
}

    /**
     * Pievieno vai noņem reakciju "patīk" komentāram.
     * 
     * Funkcionalitāte:
     * - Pārbauda lietotāja autorizāciju
     * - Pārbauda ierobežojumus
     * - Pārslēdz "patīk" statusu (toggle)
     * - Atgriež aktuālo reakciju skaitu
     */
    public function like($id)
    {
        $user = auth()->user();

        // Pārbauda, vai lietotājs ir autorizēts
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Pārbauda, vai lietotājs nav ierobežots
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari balsot par komentāriem, jo esi ierobežots'
            ], 403);
        }

        $comment = Comment::findOrFail($id);

        // Pārbauda, vai jau ir "patīk"
        $existing = $comment->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            // Noņem reakciju
            $existing->delete();
            $isLiked = false;
        } else {
            // Pievieno reakciju
            $comment->likes()->create([
                'user_id' => $user->id,
                'comment_id' => $comment->id
            ]);
            $isLiked = true;
        }

        // Atgriež rezultātu
        return response()->json([
            'likes_count' => $comment->likes()->count(),
            'is_liked' => $isLiked
        ]);
    }
}