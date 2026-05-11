<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
 {public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password
        ];

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'login' => 'Credenciales inválidas'
            ]);
        }

        // Validar que sea admin
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return back()->withErrors([
                'login' => 'No tienes permisos'
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}