<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VoterSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('voter_id')) {
            return redirect()->route('home')->with('error', 'Primero ingrese su cédula.');
        }

        return $next($request);
    }
}