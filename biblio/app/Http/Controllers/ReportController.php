<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Comment;

class ReportController extends Controller
{
    /**
     * Saglabā ziņojumu par neatbilstošu komentāru.
     *
     * Funkcionalitāte:
     * - Validē ievadīto iemeslu
     * - Pārbauda, vai lietotājs jau nav ziņojis par šo komentāru
     * - Saglabā ziņojumu datubāzē
     */
    public function store(Request $request, Comment $comment)
    {
        // Validē ievadīto iemeslu (nav obligāts)
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        // Pārbauda, vai lietotājs jau ziņojis par šo komentāru
        $alreadyReported = Report::where('comment_id', $comment->id)
            ->where('reported_by', auth()->id())
            ->exists();

        if ($alreadyReported) {
            return response()->json([
                'message' => 'You have already reported this comment.'
            ], 400);
        }

        // Izveido jaunu ziņojumu
        Report::create([
            'comment_id' => $comment->id,
            'reported_by' => auth()->id(),
            'reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}