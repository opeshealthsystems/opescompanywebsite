<?php
namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('supportProfile');
        return view('support.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:100',
            'phone'                 => 'nullable|string|max:30',
            'ticket_specialization' => 'nullable|string|in:all,technical,billing,general',
            'shift'                 => 'nullable|string|in:morning,afternoon,evening',
            'languages'             => 'nullable|string|max:500',
            'bio'                   => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        SupportProfile::updateOrCreate(['user_id' => $user->id], [
            'ticket_specialization' => $validated['ticket_specialization'] ?? 'all',
            'shift'                 => $validated['shift'] ?? null,
            'languages'             => $validated['languages'] ?? null,
            'bio'                   => $validated['bio'] ?? null,
        ]);

        return redirect()
            ->route('support.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
