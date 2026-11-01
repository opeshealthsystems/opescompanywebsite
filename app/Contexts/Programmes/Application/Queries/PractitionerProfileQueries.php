<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Models\PractitionerProfile;
use Illuminate\Support\Facades\Hash;
class PractitionerProfileQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = $request->user()->load('practitionerProfile');
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_profilecontroller_show_1', ['user' => $user, 'profile' => $user->practitionerProfile, 'professions' => \App\Models\PractitionerProfile::professionOptions()]);
    }
}
