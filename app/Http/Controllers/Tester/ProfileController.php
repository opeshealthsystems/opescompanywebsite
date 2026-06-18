<?php
namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return view('tester.profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);

        $request->user()->update($validated);

        return redirect()
            ->route('tester.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
