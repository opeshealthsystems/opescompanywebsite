<?php
namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\AccountantProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('accountantProfile');
        return view('accountant.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'                      => 'required|string|max:100',
            'phone'                     => 'nullable|string|max:30',
            'accounting_specialization' => 'nullable|string|in:general,tax,payroll,audit',
            'certifications'            => 'nullable|string|max:1000',
            'bio'                       => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);

        AccountantProfile::updateOrCreate(['user_id' => $user->id], [
            'accounting_specialization' => $validated['accounting_specialization'] ?? 'general',
            'certifications'            => $validated['certifications'] ?? null,
            'bio'                       => $validated['bio'] ?? null,
        ]);

        return redirect()
            ->route('accountant.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
