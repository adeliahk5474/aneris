<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showForm() {
        return view('auth'); // resources/views/auth.blade.php
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:client,artist'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role; // client atau artist
        $user->save();

        // Tampilkan form login setelah register
        return view('auth', ['showLogin' => true]);
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role == 'artist') {
                return redirect('/dashboard/artist');
            } else {
                return redirect('/dashboard/client');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }
}
