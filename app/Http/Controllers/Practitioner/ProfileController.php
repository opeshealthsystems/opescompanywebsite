<?php
namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Models\PractitionerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('practitionerProfile');
        return view('practitioner.profile', [
            'user'       => $user,
            'profile'    => $user->practitionerProfile,
            'professions' => PractitionerProfile::professionOptions(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'phone'             => 'nullable|string|max:30',
            'profession'        => 'required|string|in:' . implode(',', array_keys(PractitionerProfile::professionOptions())),
            'specialty'         => 'nullable|string|max:120',
            'workplace_name'    => 'nullable|string|max:150',
            'workplace_city'    => 'nullable|string|max:80',
            'workplace_country' => 'nullable|string|max:80',
            'registration_number' => 'nullable|string|max:60',
            'years_of_experience' => 'nullable|integer|min:0|max:60',
            'bio'               => 'nullable|string|max:2000',
            'opes_testimonial'  => 'nullable|string|max:1000',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $profileData = array_filter([
            'profession'          => $validated['profession'],
            'specialty'           => $validated['specialty'] ?? null,
            'workplace_name'      => $validated['workplace_name'] ?? null,
            'workplace_city'      => $validated['workplace_city'] ?? null,
            'workplace_country'   => $validated['workplace_country'] ?? 'CM',
            'registration_number' => $validated['registration_number'] ?? null,
            'years_of_experience' => $validated['years_of_experience'] ?? null,
            'bio'                 => $validated['bio'] ?? null,
            'opes_testimonial'    => $validated['opes_testimonial'] ?? null,
        ], fn ($v) => $v !== null);

        $user->practitionerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return back()->with('success', 'Password updated successfully.');
    }
}
