<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    public function create()
    {
        $voterId = session('voter_id');

        if (!$voterId) {
            return redirect()->route('home')->with('error', 'Primero ingrese su cédula.');
        }

        $voter = Voter::findOrFail($voterId);

        if ($voter->has_voted || Vote::where('voter_id', $voter->id)->exists()) {
            return redirect()->route('home')->with('error', 'Ya usted ha realizado su voto.');
        }

        $activeGrade = \App\Models\Setting::get('active_grade');

        if ($activeGrade) {
            $candidates = Candidate::where('grado', $activeGrade)->orderBy('merit_order')->get();
        } else {
            $candidates = collect([]); // Return empty if no grade is active
        }

        return view('voter.vote', compact('voter', 'candidates', 'activeGrade'));
    }

    public function store(Request $request)
    {
        $voterId = session('voter_id');

        if (!$voterId) {
            return redirect()->route('home')->with('error', 'Primero ingrese su cédula.');
        }

        $voter = Voter::findOrFail($voterId);

        if ($voter->has_voted || Vote::where('voter_id', $voter->id)->exists()) {
            return redirect()->route('home')->with('error', 'Ya usted ha realizado su voto.');
        }

        $activeGrade = \App\Models\Setting::get('active_grade');

        if ($activeGrade) {
            $candidates = Candidate::where('grado', $activeGrade)->orderBy('merit_order')->get();
        } else {
            $candidates = collect([]);
        }

        $count = $candidates->count();

        // We receive the array of candidate_id => rank. The voter might not rank everyone.
        // E.g., if they rank 4 candidates, the rank array will have 4 elements.
        $data = $request->validate([
            'rank' => ['required', 'array'],
            'rank.*' => ['nullable', 'integer', 'min:1', 'max:' . $count],
        ]);

        // Filter out empty ranks and get distinct ranks
        $submittedRanks = array_filter($data['rank'], fn($value) => !is_null($value) && $value !== '');
        
        // Ensure ranks are unique
        if (count($submittedRanks) !== count(array_unique($submittedRanks))) {
            return back()->with('error', 'No puede asignar la misma posición a más de un candidato.');
        }

        // We don't require all to be ranked.
        try {
            DB::transaction(function () use ($voter, $submittedRanks, $count) {
                foreach ($submittedRanks as $candidateId => $rank) {
                    Vote::create([
                        'voter_id' => $voter->id,
                        'candidate_id' => $candidateId,
                        'rank' => $rank,
                        'points' => ($count - (int) $rank + 1),
                    ]);

                    AuditHelper::log(
                        'vote',
                        'Vote',
                        $candidateId,
                        "Voto registrado: Votante {$voter->cedula} asignó posición {$rank} al candidato {$candidateId}"
                    );
                }

                $voter->update([
                    'has_voted' => true,
                    'voted_at' => now(),
                ]);

                AuditHelper::log(
                    'update',
                    'Voter',
                    $voter->id,
                    "Votante {$voter->cedula} completó su votación"
                );

                $this->recalculateMerit();
            });
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                return redirect()->route('home')->with('error', 'Ya usted ha realizado su voto.');
            }

            throw $e;
        }

        return redirect()
            ->route('voter.success')
            ->with('voter_id', $voter->id);
    }

    private function recalculateMerit(): void
    {
        $candidates = Candidate::all();

        foreach ($candidates as $candidate) {
            $candidate->first_place_votes = Vote::where('candidate_id', $candidate->id)
                ->where('rank', 1)
                ->count();

            $candidate->points_total = Vote::where('candidate_id', $candidate->id)->sum('points');
            $candidate->save();
        }

        $ordered = Candidate::orderByDesc('points_total')
            ->orderByDesc('first_place_votes')
            ->orderBy('merit_order') // Tie-breaker: keep their old order
            ->get();

        foreach ($ordered as $index => $candidate) {
            $candidate->update([
                'merit_order' => $index + 1,
            ]);
        }
    }
}