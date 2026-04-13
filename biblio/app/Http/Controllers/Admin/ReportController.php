<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with([
            'comment.user',
            'comment.book' // 🔥 pievienots book
        ])
        ->latest()
        ->get();

        return Inertia::render('Admin/Reports', [
            'reports' => $reports
        ]);
    }

    public function resolve(Report $report)
    {
        $report->update([
            'status' => 'resolved'
        ]);

        return back();
    }

    /* ===========================
       RESTRICT USER
    ============================ */

    public function restrict(Request $request, User $user)
    {
        $request->validate([
            'days' => 'required|integer',
            'reason' => 'nullable|string|max:500'
        ]);

        if ($request->days == 999) {
            $restrictedUntil = null;
            $status = 'blocked';
        } else {
            $restrictedUntil = now()->addDays($request->days);
            $status = 'restricted';
        }

        $user->update([
            'status' => $status,
            'restricted_until' => $restrictedUntil,
            'restriction_reason' => $request->reason
        ]);

        return back();
    }
}