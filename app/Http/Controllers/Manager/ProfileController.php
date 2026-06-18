<?php
namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ManagerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('managerProfile');
        return view('manager.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'phone'            => 'nullable|string|max:30',
            'management_level' => 'nullable|string|in:team_lead,senior_manager,director',
            'bio'              => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        ManagerProfile::updateOrCreate(['user_id' => $user->id], [
            'management_level' => $validated['management_level'] ?? null,
            'bio'              => $validated['bio'] ?? null,
        ]);

        return redirect()
            ->route('manager.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
