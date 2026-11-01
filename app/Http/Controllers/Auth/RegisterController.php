<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PractitionerWelcome;
use App\Mail\WelcomeEmail;
use App\Models\PractitionerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /** Public account types mapped to their role. Role is NEVER taken from request input. */
    private const ACCOUNT_TYPES = [
        'facility'     => 'customer',
        'individual'   => 'customer',
        'practitioner' => 'practitioner',
    ];

    public function show()
    {
        return view('auth.register', [
            'professions' => PractitionerProfile::professionOptions(),
        ]);
    }

    public function register(Request $request)
    {
        $input = new \App\Contexts\Shared\Application\WorkflowInput($request->all(), language: $request->input('locale', 'en'), address: $request->ip());
        $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute('identity.register', $input);
        $user = User::findOrFail($result->data['user_id']);
        Auth::login($user);
        $request->session()->regenerate();
        $portal = $result->data['account_type'] === 'practitioner' ? 'practitioner.dashboard' : 'customer.dashboard';
        return redirect()->route($portal, ['locale' => $result->data['locale']]);
    }
}
