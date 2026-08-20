<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request, Ad $ad): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        Report::create([
            'user_id' => Auth::id(),
            'ad_id' => $ad->id,
            'reason' => $validated['reason'],
        ]);

        return back()->with('status', 'Report submitted for review.');
    }
}
