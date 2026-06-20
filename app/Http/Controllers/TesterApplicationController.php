<?php

namespace App\Http\Controllers;

use App\Models\TesterApplication;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class TesterApplicationController extends Controller
{
    public function show()
    {
        return view('pages.join-testers');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|max:150|unique:tester_applications,email',
            'phone'            => 'nullable|string|max:30',
            'profession'       => 'required|string|max:30',
            'specialty'        => 'nullable|string|max:100',
            'institution_name' => 'nullable|string|max:150',
            'country'          => 'required|string|max:60',
            'city'             => 'nullable|string|max:60',
            'years_experience' => 'required|integer|min:0|max:50',
            'devices'          => 'nullable|array',
            'devices.*'        => 'string|max:20',
            'platforms'        => 'nullable|array',
            'platforms.*'      => 'string|max:20',
            'motivation'       => 'required|string|max:2000',
            'tech_experience'  => 'nullable|string|max:1000',
        ]);

        $app = TesterApplication::create(array_merge($validated, [
            'locale'     => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]));

        \Illuminate\Support\Facades\Notification::route('mail', $app->email)
            ->notify(new \App\Notifications\TesterApplicationReceived($app->name));

        $admins = User::role(['admin', 'super_admin'])->get();
        if ($admins->isNotEmpty()) {
            Notification::make()
                ->title('New tester application: ' . $app->name)
                ->body($app->profession . ' · ' . $app->country)
                ->icon('heroicon-o-beaker')
                ->iconColor('warning')
                ->sendToDatabase($admins);
        }

        return back()->with('success', true);
    }
}
