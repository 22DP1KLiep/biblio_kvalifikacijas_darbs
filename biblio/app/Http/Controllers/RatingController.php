<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Book;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Atgriež visus konkrētās grāmatas vērtējumus.
     * 
     * Funkcionalitāte:
     * - Iegūst visus vērtējumus pēc grāmatas ID
     */
    public function index($bookId)
    {
        return Rating::where('book_id', $bookId)->get();
    }

    /**
     * Pievieno vai atjaunina lietotāja vērtējumu grāmatai.
     * 
     * Funkcionalitāte:
     * - Pārbauda, vai lietotājs nav ierobežots
     * - Validē ievadīto vērtējumu (1–5)
     * - Ja vērtējums jau eksistē, tas tiek atjaunināts
     * - Ja neeksistē, tiek izveidots jauns ieraksts
     */
    public function store(Request $request, $bookId)
    {
        $user = auth()->user();

        // Pārbauda, vai lietotājs nav ierobežots
        if ($user && $user->isRestricted()) {
            return response()->json([
                'message' => 'Tava konta aktivitātes ir ierobežotas līdz ' 
                    . $user->restrictionEndsAt()
            ], 403);
        }

        // Datu validācija
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Vērtējuma izveide vai atjaunināšana
        return Rating::updateOrCreate(
            [
                'book_id' => $bookId,
                'user_id' => auth()->id()
            ],
            [
                'rating' => $request->rating
            ]
        );
    }

    /**
     * Atgriež pašreizējā lietotāja vērtējumu konkrētai grāmatai.
     * 
     * Funkcionalitāte:
     * - Meklē lietotāja vērtējumu pēc grāmatas ID
     * - Ja vērtējums nav piešķirts, atgriež null
     */
    public function show(Book $book)
    {
        $rating = Rating::where('book_id', $book->id)
            ->where('user_id', auth()->id())
            ->first();

        return response()->json([
            'rating' => $rating ? $rating->rating : null
        ]);
    }
}