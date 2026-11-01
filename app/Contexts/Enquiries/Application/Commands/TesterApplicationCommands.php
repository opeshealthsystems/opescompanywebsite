<?php

namespace App\Contexts\Enquiries\Application\Commands;

use App\Models\TesterApplication;
use App\Models\User;
use Filament\Notifications\Notification;
class TesterApplicationCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function submit(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['name' => 'required|string|max:100', 'email' => 'required|email|max:150|unique:tester_applications,email', 'phone' => 'nullable|string|max:30', 'profession' => 'required|string|max:30', 'specialty' => 'nullable|string|max:100', 'institution_name' => 'nullable|string|max:150', 'country' => 'required|string|max:60', 'city' => 'nullable|string|max:60', 'years_experience' => 'required|integer|min:0|max:50', 'devices' => 'nullable|array', 'devices.*' => 'string|max:20', 'platforms' => 'nullable|array', 'platforms.*' => 'string|max:20', 'motivation' => 'required|string|max:2000', 'tech_experience' => 'nullable|string|max:1000']);
        $app = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            $submission = \App\Models\TesterApplication::create(array_merge($validated, ['locale' => $request->locale(), 'ip_address' => $request->ip()]));
            app(\App\Services\WebsiteFormMailer::class)->ensure($submission);
            return $submission;
        });
        \App\Contexts\Shared\Infrastructure\DeferredWebsiteMail::deliver($app);
        \App\Contexts\Shared\Infrastructure\DomainNotificationRoute::route('mail', $app->email)->notify(new \App\Notifications\TesterApplicationReceived($app->name));
        $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
        if ($admins->isNotEmpty()) {
            \Filament\Notifications\Notification::make()->title('New tester application: ' . $app->name)->body($app->profession . ' · ' . $app->country)->icon('heroicon-o-beaker')->iconColor('warning')->sendToDatabase($admins);
        }
        return \App\Contexts\Shared\Application\WorkflowResult::next('testerapplicationcontroller_submit_1')->with('success', true);
    }
}
