<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Models\ManagerProfile;
class ManagerProfileCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function update(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['name' => 'required|string|max:100', 'phone' => 'nullable|string|max:30', 'management_level' => 'nullable|string|in:team_lead,senior_manager,director', 'bio' => 'nullable|string|max:2000']);
        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);
        \App\Models\ManagerProfile::updateOrCreate(['user_id' => $user->id], ['management_level' => $validated['management_level'] ?? null, 'bio' => $validated['bio'] ?? null]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('manager_profilecontroller_update_1', ['locale' => $request->locale()])->with('success', 'Profile updated successfully.');
    }
}
