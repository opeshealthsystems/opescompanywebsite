<?php

namespace App\Http\Controllers;

use App\Mail\LeadNotification;
use App\Models\Lead;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|max:150',
            'phone'         => 'nullable|string|max:30',
            'facility_type' => 'nullable|string|max:60',
            'products'      => 'nullable|string|max:255',
            'message'       => 'nullable|string|max:2000',
            'product_slug'  => 'nullable|string|max:60',
        ]);

        $lead = Lead::create(array_merge($validated, [
            'source'     => $request->filled('product_slug') ? 'product-page' : 'contact',
            'locale'     => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]));

        $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL'));
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new LeadNotification($lead));
        }

        $admins = User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::make()
                ->title('New lead: ' . $lead->name)
                ->body($lead->email . ($lead->facility_type ? ' · ' . $lead->facility_type : ''))
                ->icon('heroicon-o-user-plus')
                ->iconColor('success')
                ->sendToDatabase($admins);
        }

        return back()->with('success', 'Thank you! Our team will contact you within one business day.');
    }
}
