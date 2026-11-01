<?php

namespace App\Contexts\Enquiries\Application\Commands;

use App\Models\DemoRequest;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;
class DemoRequestCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function submit(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['name' => 'required|string|max:100', 'email' => 'required|email|max:150', 'phone' => 'nullable|string|max:30', 'organization_name' => 'required|string|max:150', 'country' => 'nullable|string|max:60', 'institution_type' => 'nullable|string|max:40', 'institution_size' => 'nullable|string|max:20', 'products' => 'nullable|array', 'products.*' => 'string|max:60', 'message' => 'nullable|string|max:2000', 'preferred_date' => 'nullable|date|after:today']);
        $demo = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            $submission = \App\Models\DemoRequest::create(array_merge($validated, ['locale' => $request->locale(), 'ip_address' => $request->ip()]));
            app(\App\Services\WebsiteFormMailer::class)->ensure($submission);
            return $submission;
        });
        \App\Contexts\Shared\Infrastructure\DeferredWebsiteMail::deliver($demo);
        \App\Contexts\Shared\Infrastructure\DomainNotificationRoute::route('mail', $demo->email)->notify(new \App\Notifications\DemoRequestConfirmation($demo->name));
        $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
        if ($admins->isNotEmpty()) {
            \Filament\Notifications\Notification::make()->title('New demo request: ' . $demo->organization_name)->body($demo->name . ' · ' . $demo->email . ($demo->institution_type ? ' · ' . $demo->institution_type : ''))->icon('heroicon-o-calendar')->iconColor('info')->sendToDatabase($admins);
        }
        return \App\Contexts\Shared\Application\WorkflowResult::next('demorequestcontroller_submit_1')->with('success', true);
    }
}
