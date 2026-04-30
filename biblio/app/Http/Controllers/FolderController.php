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
        $user = Auth::user();

        // 🔒 RESTRICTED BLOCK
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari izveidot mapes, jo Tavs konts ir ierobežots'
            ], 403);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:folders,name,NULL,id,user_id,' . $user->id
            ]
        ]);

        $folder = $user->folders()->create([
            'name' => $request->name,
            'is_public' => $request->is_public ?? false
        ]);

        return response()->json($folder);
    }

    public function books(Folder $folder)
    {
        if ($folder->user_id !== Auth::id() && !$folder->is_public) {
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
        $user = Auth::user();

        // 🔒 RESTRICTED BLOCK
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari pievienot grāmatas mapēm, jo esi ierobežots'
            ], 403);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        if ($folder->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->books()->syncWithoutDetaching([$request->book_id]);

        return response()->json(['message' => 'Book added to folder']);
    }

    public function removeBook(Folder $folder, Book $book)
    {
        $user = Auth::user();

        // 🔒 RESTRICTED BLOCK
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari noņemt grāmatas no mapēm'
            ], 403);
        }

        if ($folder->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->books()->detach($book->id);

        $folder->load(['books.genres']);

        return response()->json([
            'message' => 'Book removed from folder',
            'books' => $folder->books
        ]);
    }

    public function destroy(Folder $folder)
    {
        $user = Auth::user();

        // 🔒 RESTRICTED BLOCK
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari dzēst mapes'
            ], 403);
        }

        if ($folder->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->books()->detach();
        $folder->delete();

        return response()->json(['message' => 'Mape dzēsta veiksmīgi']);
    }

    public function toggleVisibility(Folder $folder)
    {
        $user = Auth::user();

        // 🔒 RESTRICTED BLOCK
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari mainīt mapes redzamību'
            ], 403);
        }

        if ($folder->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->is_public = !$folder->is_public;
        $folder->save();

        return response()->json([
            'is_public' => $folder->is_public
        ]);
    }

    public function show(Folder $folder)
    {
        if ($folder->user_id !== auth()->id() && !$folder->is_public) {
            abort(403);
        }

        $folder->load('books.genres');

        return Inertia::render('Folders/Show', [
            'folder' => $folder,
            'books' => $folder->books,
        ]);
    }
}