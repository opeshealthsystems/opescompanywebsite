<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users|max:150',
            'password'      => 'required|string|min:8|confirmed',
            'phone'         => 'nullable|string|max:30',
            'facility_name' => 'nullable|string|max:100',
            'facility_type' => 'nullable|string|max:60',
            'country'       => 'required|string|max:60',
            'city'          => 'nullable|string|max:60',
            'locale'        => 'nullable|string|in:en,fr',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => $validated['password'],
                'phone'     => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            $user->assignRole('customer');

            $user->customerProfile()->create([
                'facility_name' => $validated['facility_name'] ?? null,
                'facility_type' => $validated['facility_type'] ?? null,
                'country'       => $validated['country'],
                'city'          => $validated['city'] ?? null,
            ]);

            return $user;
        });

        Auth::login($user);

        $locale = $validated['locale'] ?? 'en';
        return redirect()->route('customer.dashboard', ['locale' => $locale]);
    }
}
