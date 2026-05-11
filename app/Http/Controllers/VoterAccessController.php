<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoterAccessController extends Controller
{
    public function enter(Request $request)
    {
        $data = $request->validate([
            'cedula' => ['required', 'string', 'max:30'],
        ]);

        $voter = Voter::where('cedula', $data['cedula'])->first();

        if (!$voter) {
            return back()->with('error', 'La cédula no está registrada.');
        }

        if ($voter->has_voted || Vote::where('voter_id', $voter->id)->exists()) {
            return redirect()->route('home')
                ->with('error', '⚠️ Usted ya ejerció su voto.');
        }

        session(['voter_id' => $voter->id]);

        return redirect()->route('vote.create');
    }
}