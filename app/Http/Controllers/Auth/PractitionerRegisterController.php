<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PractitionerWelcome;
use App\Models\PractitionerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PractitionerRegisterController extends Controller
{
    public function show()
    {
        return view('auth.practitioner-register', [
            'professions' => PractitionerProfile::professionOptions(),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'email'             => 'required|email|unique:users|max:150',
            'password'          => 'required|string|min:8|confirmed',
            'phone'             => 'nullable|string|max:30',
            'profession'        => 'required|string|in:' . implode(',', array_keys(PractitionerProfile::professionOptions())),
            'specialty'         => 'nullable|string|max:120',
            'workplace_name'    => 'nullable|string|max:150',
            'workplace_country' => 'nullable|string|max:80',
            'locale'            => 'nullable|string|in:en,fr',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => $validated['password'],
                'phone'     => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            $user->assignRole('practitioner');

            $user->practitionerProfile()->create([
                'profession'        => $validated['profession'],
                'specialty'         => $validated['specialty'] ?? null,
                'workplace_name'    => $validated['workplace_name'] ?? null,
                'workplace_country' => $validated['workplace_country'] ?? 'CM',
            ]);

            return $user;
        });

        Auth::login($user);

        Mail::to($user->email)->queue(new PractitionerWelcome($user));

        $locale = $validated['locale'] ?? 'en';
        return redirect()->route('practitioner.dashboard', ['locale' => $locale]);
    }
}
