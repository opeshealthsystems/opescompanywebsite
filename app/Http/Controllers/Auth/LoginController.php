<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'locale'   => 'nullable|string|in:en,fr',
        ]);

        $locale = $credentials['locale'] ?? 'en';
        unset($credentials['locale']);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->hasAnyRole(['super_admin', 'admin', 'support'])) {
            return redirect('/admin');
        }

        if ($user->hasRole('practitioner')) {
            return redirect()->route('practitioner.dashboard', ['locale' => $locale]);
        }

        if ($user->hasRole('tester')) {
            return redirect()->route('tester.dashboard', ['locale' => $locale]);
        }

        return redirect()->route('customer.dashboard', ['locale' => $locale]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
