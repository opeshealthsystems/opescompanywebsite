<?php

namespace App\Contexts\Enquiries\Application\Commands;

use App\Models\PartnerApplication;
use App\Models\User;
use Filament\Notifications\Notification;
class PartnerApplicationCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function submit(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['organization_name' => 'required|string|max:150', 'contact_name' => 'required|string|max:100', 'email' => 'required|email|max:150', 'phone' => 'nullable|string|max:30', 'country' => 'required|string|max:60', 'city' => 'nullable|string|max:60', 'partner_type' => ['required', 'string', 'max:30', \Illuminate\Validation\Rule::in(array_keys(PartnerApplication::$partnerTypes))], 'organization_type' => 'nullable|string|max:30', 'annual_revenue_range' => 'nullable|string|max:30', 'target_market' => 'nullable|string|max:500', 'description' => 'required|string|max:3000', 'website' => 'nullable|url|max:200']);
        $app = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            $submission = \App\Models\PartnerApplication::create(array_merge($validated, ['locale' => $request->locale(), 'ip_address' => $request->ip()]));
            app(\App\Services\WebsiteFormMailer::class)->ensure($submission);
            return $submission;
        });
        \App\Contexts\Shared\Infrastructure\DeferredWebsiteMail::deliver($app);
        \App\Contexts\Shared\Infrastructure\DomainNotificationRoute::route('mail', $app->email)->notify(new \App\Notifications\PartnerApplicationReceived($app->contact_name, $app->organization_name));
        $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
        if ($admins->isNotEmpty()) {
            \Filament\Notifications\Notification::make()->title('New partner application: ' . $app->organization_name)->body($app->contact_name . ' · ' . $app->partner_type . ' · ' . $app->country)->icon('heroicon-o-users')->iconColor('success')->sendToDatabase($admins);
        }
        if ($request->api) {
            return \App\Contexts\Shared\Application\WorkflowResult::payload([
                'status' => 'submitted',
                'received_at' => $app->created_at?->toIso8601String(),
            ], 201);
        }
        return \App\Contexts\Shared\Application\WorkflowResult::next('partnerapplicationcontroller_submit_1')->with('success', true);
    }
}
