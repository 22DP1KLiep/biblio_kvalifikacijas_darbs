<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Rāda 10 grāmatas ar žanriem (homepage, utt.)
     */
    public function index()
    {
        $books = Book::with('genres')->limit(10)->get();
        return response()->json($books);
    }

    /**
     * Rāda visas grāmatas ar žanriem
     */
    public function get_all(Request $request)
    {
        $sortField = $request->query('sort', 'title');
        $direction = $request->query('direction', 'asc');
        $genreIds = $request->query('genres', []); // <-- no vaicājuma

        $allowedFields = ['title', 'author', 'published_year'];
        if (!in_array($sortField, $allowedFields)) {
            $sortField = 'title';
        }

        $books = Book::with('genres')
            ->when(!empty($genreIds), function ($query) use ($genreIds) {
                $query->whereHas('genres', function ($q) use ($genreIds) {
                    $q->whereIn('genres.id', $genreIds);
                }); // <-- NO count() here – tas tagad ir OR
            })
            ->orderBy($sortField, $direction)
            ->get();


        return response()->json($books);
    }


    /**
     * Parāda vienu grāmatu ar žanriem
     */
    public function show($id)
{
    $book = Book::with([
        'genres',
        'comments' => function ($query) {
            $query->with('user')        // lai redz komentētāju
                  ->withCount('likes')  // lai redz like skaitu
                  ->orderBy('created_at', 'desc');
        }
    ])->findOrFail($id);

    return response()->json($book);
}

    
}
