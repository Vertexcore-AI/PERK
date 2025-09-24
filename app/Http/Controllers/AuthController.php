<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        // Check if login field contains '@' to determine if it's email or username
        $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Prepare credentials for authentication
        $authCredentials = [
            $loginField => $credentials['email'],
            'password' => $credentials['password']
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($authCredentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.']
        ]);
    }

    /**
     * Log the user out of the application
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}