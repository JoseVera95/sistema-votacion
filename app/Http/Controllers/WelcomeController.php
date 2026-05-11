<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Voter;

class WelcomeController extends Controller
{
    public function index()
    {
        $lastVoter = null;

        return view('welcome', compact('lastVoter'));
    }
}