<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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

        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        if ($user->hasAnyRole(['super_admin', 'admin', 'support'])) {
            return redirect('/admin');
        }

        $portalRoutes = [
            'practitioner' => 'practitioner.dashboard',
            'tester'       => 'tester.dashboard',
            'manager'      => 'manager.dashboard',
            'hr'           => 'hr.dashboard',
            'accountant'   => 'accountant.dashboard',
        ];
        foreach ($portalRoutes as $role => $routeName) {
            if ($user->hasRole($role)) {
                return redirect()->route($routeName, ['locale' => $locale]);
            }
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
