<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user    = Auth::user();
        $profile = $user->customerProfile ?? $user->customerProfile()->create(['country' => 'CM']);

        return view('customer.profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'phone'         => 'nullable|string|max:30',
            'facility_name' => 'nullable|string|max:100',
            'facility_type' => 'nullable|string|max:60',
            'country'       => 'required|string|max:60',
            'city'          => 'nullable|string|max:60',
            'address'       => 'nullable|string|max:200',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $profileData = [
            'facility_name' => $validated['facility_name'] ?? null,
            'facility_type' => $validated['facility_type'] ?? null,
            'country'       => $validated['country'],
            'city'          => $validated['city'] ?? null,
            'address'       => $validated['address'] ?? null,
        ];

        if ($user->customerProfile) {
            $user->customerProfile->update($profileData);
        } else {
            $user->customerProfile()->create(array_merge($profileData, ['user_id' => $user->id]));
        }

        $locale = $request->route('locale') ?? 'en';

        return redirect()
            ->route('customer.profile', ['locale' => $locale])
            ->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        $locale = $request->route('locale') ?? 'en';

        return redirect()
            ->route('customer.profile', ['locale' => $locale])
            ->with('success', 'Password changed successfully.');
    }
}
