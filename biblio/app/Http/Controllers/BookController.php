<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Atgriež visas grāmatas ar filtrēšanas un kārtošanas iespējām.
     * 
     * Parametri:
     * - sort: kārtošanas lauks (title, author, published_year)
     * - direction: kārtošanas virziens (asc, desc)
     * - genres: žanru ID masīvs filtrēšanai
     */
    public function get_all(Request $request)
    {
        // Iegūst vaicājuma parametrus
        $sortField = $request->query('sort', 'title');
        $direction = $request->query('direction', 'asc');
        $genreIds = $request->query('genres', []);

        // Atļautie kārtošanas lauki drošībai
        $allowedFields = ['title', 'author', 'published_year'];
        if (!in_array($sortField, $allowedFields)) {
            $sortField = 'title';
        }

        // Vaicājums ar filtrēšanu un kārtošanu
        $books = Book::with('genres')
            ->when(!empty($genreIds), function ($query) use ($genreIds) {
                $query->whereHas('genres', function ($q) use ($genreIds) {
                    $q->whereIn('genres.id', $genreIds);
                });
            })
            ->orderBy($sortField, $direction)
            ->get();

        return response()->json($books);
    }

    /**
     * Atgriež vienas grāmatas detalizētu informāciju.
     * Iekļauj:
     * - žanrus
     * - mapes
     * - komentārus ar lietotājiem un reakcijām
     */
    public function show($id)
    {
        $book = Book::with([
            'genres',
            'folders',
            'comments' => function ($query) {
                $query->with('user')
                    ->with(['likes' => function ($q) {
                        if (auth()->check()) {
                            $q->where('user_id', auth()->id());
                        }
                    }])
                    ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        return response()->json($book);
    }

    /**
     * Atgriež visas grāmatas, kas atrodas lietotāja mapēs.
     * Tiek noņemti dublikāti.
     */
    public function userBooks()
    {
        $user = auth()->user();

        $books = $user->folders()
            ->with('books')
            ->get()
            ->pluck('books')
            ->flatten()
            ->unique('id')
            ->values();

        return response()->json($books);
    }
}