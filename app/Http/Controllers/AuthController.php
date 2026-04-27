<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    // Show login & register form
    public function showAuthForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.auth');
    }

    // Register user
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|in:artist,client'
        ]);

        $redirectTo = route('auth.verify');

        $supabaseResponse = $this->supabase->signUp($request->email, $request->password, $redirectTo);

        if (isset($supabaseResponse['error'])) {
            return back()->withErrors(['msg' => $supabaseResponse['error']['message']]);
        }

        // Save user temporarily before verified
        User::create([
            'user_id' => Str::uuid(),
            'name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('auth.form')->with('success', 'Registration successful. Please check your email for verification.');
    }

    // Email verification
    public function verify(Request $request)
    {
        $access_token = $request->query('access_token');

        if (!$access_token) {
            return redirect()->route('auth.form')->withErrors(['msg' => 'Verification link invalid or expired']);
        }

        $response = $this->supabase->verifyEmail($access_token);

        if (isset($response['error'])) {
            return redirect()->route('auth.form')->withErrors(['msg' => $response['error']['message']]);
        }

        return redirect()->route('auth.form')->with('success', 'Email verified. You may now login.');
    }

    // Login user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home')); // Always go back to last page or home
        }

        return back()->withErrors(['msg' => 'Invalid credentials']);
    }

    // Logout user
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
