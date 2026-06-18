<?php

namespace App\Http\Controllers;

use App\Models\PartnerApplication;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class PartnerApplicationController extends Controller
{
    public function show()
    {
        return view('pages.become-a-partner');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'organization_name'    => 'required|string|max:150',
            'contact_name'         => 'required|string|max:100',
            'email'                => 'required|email|max:150',
            'phone'                => 'nullable|string|max:30',
            'country'              => 'required|string|max:60',
            'city'                 => 'nullable|string|max:60',
            'partner_type'         => 'required|string|max:30',
            'organization_type'    => 'nullable|string|max:30',
            'annual_revenue_range' => 'nullable|string|max:30',
            'target_market'        => 'nullable|string|max:500',
            'description'          => 'required|string|max:3000',
            'website'              => 'nullable|url|max:200',
        ]);

        $app = PartnerApplication::create(array_merge($validated, [
            'locale'     => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]));

        $admins = User::role(['admin', 'super_admin'])->get();
        if ($admins->isNotEmpty()) {
            Notification::make()
                ->title('New partner application: ' . $app->organization_name)
                ->body($app->contact_name . ' · ' . $app->partner_type . ' · ' . $app->country)
                ->icon('heroicon-o-handshake')
                ->iconColor('success')
                ->sendToDatabase($admins);
        }

        return back()->with('success', true);
    }
}
