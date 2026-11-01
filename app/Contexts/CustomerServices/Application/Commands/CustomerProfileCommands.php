<?php

namespace App\Contexts\CustomerServices\Application\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class CustomerProfileCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function update(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $validated = $request->validate(['name' => 'required|string|max:100', 'phone' => 'nullable|string|max:30', 'facility_name' => 'nullable|string|max:100', 'facility_type' => 'nullable|string|max:60', 'country' => 'required|string|max:60', 'city' => 'nullable|string|max:60', 'address' => 'nullable|string|max:200']);
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);
        $profileData = ['facility_name' => $validated['facility_name'] ?? null, 'facility_type' => $validated['facility_type'] ?? null, 'country' => $validated['country'], 'city' => $validated['city'] ?? null, 'address' => $validated['address'] ?? null];
        if ($user->customerProfile) {
            $user->customerProfile->update($profileData);
        } else {
            $user->customerProfile()->create(array_merge($profileData, ['user_id' => $user->id]));
        }
        $locale = $request->route('locale') ?? 'en';
        return \App\Contexts\Shared\Application\WorkflowResult::next('customer_profilecontroller_update_1', ['locale' => $locale])->with('success', 'Profile updated successfully.');
    }
    public function changePassword(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $request->validate(['current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
            if (!\Illuminate\Support\Facades\Hash::check($value, $user->password)) {
                $fail('The current password is incorrect.');
            }
        }], 'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()]]);
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password)]);
        $locale = $request->route('locale') ?? 'en';
        return \App\Contexts\Shared\Application\WorkflowResult::next('customer_profilecontroller_changePassword_1', ['locale' => $locale])->with('success', 'Password changed successfully.');
    }
}
