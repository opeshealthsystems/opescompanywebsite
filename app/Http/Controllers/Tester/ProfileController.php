<?php

namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\TesterProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('testerProfile');
        return view('tester.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'phone'              => 'nullable|string|max:30',
            'testing_specialty'  => 'nullable|string|in:web,mobile,api,desktop',
            'device_types'       => 'nullable|string|max:1000',
            'portfolio_url'      => 'nullable|url|max:255',
            'certifications'     => 'nullable|string|max:1000',
            'availability_notes' => 'nullable|string|max:1000',
            'bio'                => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        TesterProfile::updateOrCreate(['user_id' => $user->id], array_filter([
            'testing_specialty'  => $validated['testing_specialty'] ?? null,
            'device_types'       => $validated['device_types'] ?? null,
            'portfolio_url'      => $validated['portfolio_url'] ?? null,
            'certifications'     => $validated['certifications'] ?? null,
            'availability_notes' => $validated['availability_notes'] ?? null,
            'bio'                => $validated['bio'] ?? null,
        ], fn($v) => $v !== null));

        return redirect()
            ->route('tester.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
