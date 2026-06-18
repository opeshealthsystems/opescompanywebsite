<?php
namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('employeeProfile');
        return view('hr.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'                     => 'required|string|max:100',
            'phone'                    => 'nullable|string|max:30',
            'emergency_contact_name'   => 'nullable|string|max:100',
            'emergency_contact_phone'  => 'nullable|string|max:30',
            'emergency_contact_relation' => 'nullable|string|max:50',
            'notes'                    => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        EmployeeProfile::updateOrCreate(['user_id' => $user->id], [
            'emergency_contact_name'     => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone'    => $validated['emergency_contact_phone'] ?? null,
            'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null,
            'notes'                      => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('hr.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
