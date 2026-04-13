<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([], 200);
        }

        $folders = Auth::user()->folders()->with('books')->get();
        return response()->json($folders);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $folder = Auth::user()->folders()->create([
            'name' => $request->name
        ]);

        return response()->json($folder);
    }

    public function books(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->load(['books.genres']);

        return response()->json([
            'folder' => $folder,
            'books' => $folder->books
        ]);
    }

    public function addBook(Request $request, Folder $folder)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        if ($folder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->books()->syncWithoutDetaching([$request->book_id]);

        return response()->json(['message' => 'Book added to folder']);
    }

    public function removeBook(Folder $folder, Book $book)
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->books()->detach($book->id);

        // Ielādē atlikušo grāmatu sarakstu ar žanriem
        $folder->load(['books.genres']);

        return response()->json([
            'message' => 'Book removed from folder',
            'books' => $folder->books
        ]);
    }

    public function destroy(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->books()->detach();
        $folder->delete();

        return response()->json(['message' => 'Mape dzēsta veiksmīgi']);
    }


}
