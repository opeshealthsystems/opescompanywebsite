<?php

namespace App\Contexts\Finance\Application\Commands;

use App\Models\AccountantProfile;
class AccountantProfileCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function update(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['name' => 'required|string|max:100', 'phone' => 'nullable|string|max:30', 'accounting_specialization' => 'nullable|string|in:general,tax,payroll,audit', 'certifications' => 'nullable|string|max:1000', 'bio' => 'nullable|string|max:2000']);
        $user = $request->user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone'] ?? null]);
        \App\Models\AccountantProfile::updateOrCreate(['user_id' => $user->id], ['accounting_specialization' => $validated['accounting_specialization'] ?? 'general', 'certifications' => $validated['certifications'] ?? null, 'bio' => $validated['bio'] ?? null]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('accountant_profilecontroller_update_1', ['locale' => $request->locale()])->with('success', 'Profile updated successfully.');
    }
}
