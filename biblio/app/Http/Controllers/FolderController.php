<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Folder;
use App\Models\Book;

class FolderController extends Controller
{
    /**
     * Atgriež visas lietotāja mapes ar tajās esošajām grāmatām.
     */
    public function index()
    {
        // Ja lietotājs nav autorizēts, atgriež tukšu sarakstu
        if (!Auth::check()) {
            return response()->json([], 200);
        }

        // Iegūst lietotāja mapes ar grāmatām
        $folders = Auth::user()
            ->folders()
            ->with('books')
            ->get();

        return response()->json($folders);
    }

    /**
     * Izveido jaunu mapi.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Pārbauda, vai lietotājs nav ierobežots
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari izveidot mapes, jo Tavs konts ir ierobežots'
            ], 403);
        }

        // Validē ievadītos datus
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:folders,name,NULL,id,user_id,' . $user->id
            ]
        ]);

        // Izveido mapi
        $folder = $user->folders()->create([
            'name' => $request->name,
            'is_public' => $request->is_public ?? false
        ]);

        return response()->json($folder);
    }

    /**
     * Atgriež konkrētās mapes grāmatas.
     */
    public function books(Folder $folder)
    {
        // Pārbauda piekļuvi (privāta vai publiska mape)
        if ($folder->user_id !== Auth::id() && !$folder->is_public) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ielādē grāmatas ar žanriem
        $folder->load(['books.genres']);

        return response()->json([
            'folder' => $folder,
            'books' => $folder->books
        ]);
    }

    /**
     * Pievieno grāmatu mapē (many-to-many relācija).
     */
    public function addBook(Request $request, Folder $folder)
    {
        $user = Auth::user();

        // Pārbauda ierobežojumus
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari pievienot grāmatas mapēm, jo esi ierobežots'
            ], 403);
        }

        // Validē grāmatas ID
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        // Pārbauda īpašumtiesības
        if ($folder->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Pievieno grāmatu, neizdzēšot esošās
        $folder->books()->syncWithoutDetaching([$request->book_id]);

        return response()->json(['message' => 'Book added to folder']);
    }

    /**
     * Noņem grāmatu no mapes.
     */
    public function removeBook(Folder $folder, Book $book)
    {
        $user = Auth::user();

        // Pārbauda ierobežojumus
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari noņemt grāmatas no mapēm'
            ], 403);
        }

        // Pārbauda īpašumtiesības
        if ($folder->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Noņem grāmatu no mapes
        $folder->books()->detach($book->id);

        // Ielādē atjaunināto sarakstu
        $folder->load(['books.genres']);

        return response()->json([
            'message' => 'Book removed from folder',
            'books' => $folder->books
        ]);
    }

    /**
     * Dzēš mapi.
     */
    public function destroy(Folder $folder)
    {
        $user = Auth::user();

        // Pārbauda ierobežojumus
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari dzēst mapes'
            ], 403);
        }

        // Pārbauda īpašumtiesības
        if ($folder->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Noņem visas saistītās grāmatas un dzēš mapi
        $folder->books()->detach();
        $folder->delete();

        return response()->json([
            'message' => 'Mape dzēsta veiksmīgi'
        ]);
    }

    /**
     * Maina mapes redzamību (publiska / privāta).
     */
    public function toggleVisibility(Folder $folder)
    {
        $user = Auth::user();

        // Pārbauda ierobežojumus
        if ($user->isRestricted()) {
            return response()->json([
                'message' => 'Tu nevari mainīt mapes redzamību'
            ], 403);
        }

        // Pārbauda īpašumtiesības
        if ($folder->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Pārslēdz redzamību
        $folder->is_public = !$folder->is_public;
        $folder->save();

        return response()->json([
            'is_public' => $folder->is_public
        ]);
    }

    /**
     * Attēlo mapes lapu frontendā.
     */
    public function show(Folder $folder)
    {
        // Pārbauda piekļuvi
        if ($folder->user_id !== auth()->id() && !$folder->is_public) {
            abort(403);
        }

        // Ielādē grāmatas ar žanriem
        $folder->load('books.genres');

        return Inertia::render('Folders/Show', [
            'folder' => $folder,
            'books' => $folder->books,
        ]);
    }
}