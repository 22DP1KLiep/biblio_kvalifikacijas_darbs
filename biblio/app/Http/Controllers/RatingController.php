<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Book;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index($bookId)
    {
        return Rating::where('book_id', $bookId)->get();
    }

    public function store(Request $request, $bookId)
    {
        //Validate if user is restricted
        if (auth()->user()->isRestricted()) {
        return response()->json([
            'message' => 'Tava konta aktivitātes ir ierobežotas līdz ' 
                . auth()->user()->restrictionEndsAt()
        ], 403);
    }
    
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        return Rating::updateOrCreate(
            ['book_id' => $bookId, 'user_id' => auth()->id()],
            ['rating' => $request->rating]
        );
    }

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
