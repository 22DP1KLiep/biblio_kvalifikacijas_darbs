<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    // Attēlo visus lietotāju iesniegtos ziņojumus
    public function index()
    {
        $reports = Report::with([
            'comment.user', // komentāra autors
            'comment.book', // grāmata, pie kuras atrodas komentārs
            'user'          // lietotājs, kurš iesniedza ziņojumu
        ])
        ->latest()
        ->get();

        return Inertia::render('Admin/Reports', [
            'reports' => $reports
        ]);
    }

    // Atzīmē ziņojumu kā izskatītu
    public function resolve(Report $report)
    {
        $report->update([
            'status' => 'resolved'
        ]);

        return back();
    }

    /*Lietotāja ierobežošana*/

    // Uzliek lietotājam ierobežojumu vai bloķē kontu
    public function restrict(Request $request, User $user)
    {
        $request->validate([
            'days' => 'required|integer',
            'reason' => 'nullable|string|max:500'
        ]);

        // Ja norādītas 999 dienas, lietotājs tiek bloķēts uz nenoteiktu laiku
        if ($request->days == 999) {
            $restrictedUntil = null;
            $status = 'blocked';
        } else {
            // Pretējā gadījumā tiek uzlikts pagaidu ierobežojums
            $restrictedUntil = now()->addDays($request->days);
            $status = 'restricted';
        }

        // Saglabā ierobežojuma informāciju lietotāja profilā
        $user->update([
            'status' => $status,
            'restricted_until' => $restrictedUntil,
            'restriction_reason' => $request->reason
        ]);

        return back();
    }
}