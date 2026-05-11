<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Voter;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVoters = Voter::count();
        $votedVoters = Voter::where('has_voted', true)->count();
        $pendingVoters = $totalVoters - $votedVoters;
        $totalCandidates = Candidate::count();
        $topCandidate = Candidate::orderByDesc('points_total')->orderByDesc('first_place_votes')->first();

        $recentCandidates = Candidate::orderBy('merit_order')->take(5)->get();
        $recentVoters = Voter::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalVoters',
            'votedVoters',
            'pendingVoters',
            'totalCandidates',
            'topCandidate',
            'recentCandidates',
            'recentVoters'
        ));
    }
}