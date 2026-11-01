<?php

namespace App\Contexts\Support\Application\Commands;

use App\Models\SupportProfile;
class SupportProfileCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function update(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['name' => 'required|string|max:100', 'phone' => 'nullable|string|max:30', 'ticket_specialization' => 'nullable|string|in:all,technical,billing,general', 'shift' => 'nullable|string|in:morning,afternoon,evening', 'languages' => 'nullable|string|max:500', 'bio' => 'nullable|string|max:2000']);
        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);
        \App\Models\SupportProfile::updateOrCreate(['user_id' => $user->id], ['ticket_specialization' => $validated['ticket_specialization'] ?? 'all', 'shift' => $validated['shift'] ?? null, 'languages' => $validated['languages'] ?? null, 'bio' => $validated['bio'] ?? null]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('support_profilecontroller_update_1', ['locale' => $request->locale()])->with('success', 'Profile updated successfully.');
    }
}
