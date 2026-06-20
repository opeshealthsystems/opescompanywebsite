<?php

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class DemoRequestController extends Controller
{
    public function show()
    {
        $products = Product::orderBy('name')->get();
        return view('pages.book-demo', compact('products'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'email'             => 'required|email|max:150',
            'phone'             => 'nullable|string|max:30',
            'organization_name' => 'required|string|max:150',
            'country'           => 'nullable|string|max:60',
            'institution_type'  => 'nullable|string|max:40',
            'institution_size'  => 'nullable|string|max:20',
            'products'          => 'nullable|array',
            'products.*'        => 'string|max:60',
            'message'           => 'nullable|string|max:2000',
            'preferred_date'    => 'nullable|date|after:today',
        ]);

        $demo = DemoRequest::create(array_merge($validated, [
            'locale'     => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]));

        \Illuminate\Support\Facades\Notification::route('mail', $demo->email)
            ->notify(new \App\Notifications\DemoRequestConfirmation($demo->name));

        $admins = User::role(['admin', 'super_admin'])->get();
        if ($admins->isNotEmpty()) {
            Notification::make()
                ->title('New demo request: ' . $demo->organization_name)
                ->body($demo->name . ' · ' . $demo->email . ($demo->institution_type ? ' · ' . $demo->institution_type : ''))
                ->icon('heroicon-o-calendar')
                ->iconColor('info')
                ->sendToDatabase($admins);
        }

        return back()->with('success', true);
    }
}
