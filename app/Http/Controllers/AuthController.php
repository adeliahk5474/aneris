<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\User;
use App\Services\SupabaseService;

class AuthController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    // ===============================
    // SHOW AUTH PAGE
    // ===============================
    public function showAuthForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.auth');
    }

    // ===============================
    // REGISTER
    // ===============================
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|in:artist,client'
        ]);

        // ===============================
        // SUPABASE AUTH REGISTER
        // ===============================
        $supabaseResponse = $this->supabase->signUp(
            $request->email,
            $request->password
        );

        // ERROR SUPABASE
        if (isset($supabaseResponse['error'])) {

            return back()->withErrors([
                'msg' => $supabaseResponse['error']['message']
            ]);

        }

        // ===============================
        // SAVE USER LOCAL DATABASE
        // ===============================
        User::create([
            'user_id' => Str::uuid(),

            'name' => $request->full_name,
            'email' => $request->email,

            'password' => Hash::make($request->password),

            'role' => $request->role
        ]);

        // ===============================
        // REDIRECT LOGIN
        // ===============================
        return redirect()
            ->route('auth.form')
            ->with('success', 'Registration successful. Please login.');
    }

    // ===============================
    // LOGIN
    // ===============================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        // ===============================
        // LOGIN LOCAL LARAVEL
        // ===============================
        if (Auth::attempt($credentials, $request->remember)) {

            $request->session()->regenerate();

            return redirect()->route('home');
        }

        return back()->withErrors([
            'msg' => 'Invalid credentials'
        ]);
    }

    // ===============================
    // LOGOUT
    // ===============================
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
