<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Models\EmployeeProfile;
class HrProfileCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function update(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['name' => 'required|string|max:100', 'phone' => 'nullable|string|max:30', 'emergency_contact_name' => 'nullable|string|max:100', 'emergency_contact_phone' => 'nullable|string|max:30', 'emergency_contact_relation' => 'nullable|string|max:50', 'notes' => 'nullable|string|max:2000']);
        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);
        \App\Models\EmployeeProfile::updateOrCreate(['user_id' => $user->id], ['emergency_contact_name' => $validated['emergency_contact_name'] ?? null, 'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null, 'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null, 'notes' => $validated['notes'] ?? null]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('hr_profilecontroller_update_1', ['locale' => $request->locale()])->with('success', 'Profile updated successfully.');
    }
}
