<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Comment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, Comment $comment)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        // Neļaujam ziņot vairākas reizes par to pašu komentāru
        $alreadyReported = Report::where('comment_id', $comment->id)
            ->where('reported_by', auth()->id())
            ->exists();

        if ($alreadyReported) {
            return response()->json([
                'message' => 'You have already reported this comment.'
            ], 400);
        }

        Report::create([
            'comment_id' => $comment->id,
            'reported_by' => auth()->id(),
            'reason' => $request->reason,
        ]);

        return response()->json(['success' => true]);
    }
}