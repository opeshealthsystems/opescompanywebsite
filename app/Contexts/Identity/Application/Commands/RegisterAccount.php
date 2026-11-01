<?php

namespace App\Contexts\Identity\Application\Commands;

use App\Mail\PractitionerWelcome;
use App\Mail\WelcomeEmail;
use App\Models\PractitionerProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class RegisterAccount extends \App\Contexts\Shared\Application\WorkflowHandler
{
    private const ACCOUNT_TYPES = [
        'facility'     => 'customer',
        'individual'   => 'customer',
        'practitioner' => 'practitioner',
    ];

    public function register(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $accountType = $request->input('account_type');

        $rules = [
            'account_type' => ['required', Rule::in(array_keys(self::ACCOUNT_TYPES))],
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users|max:150',
            'password'     => 'required|string|min:8|confirmed',
            'phone'        => 'nullable|string|max:30',
            'locale'       => 'nullable|string|in:en,fr',
        ];

        if ($accountType === 'facility') {
            $rules += [
                'facility_name' => 'required|string|max:100',
                'facility_type' => 'required|string|max:60',
                'country'       => 'required|string|max:60',
                'city'          => 'nullable|string|max:60',
            ];
        } elseif ($accountType === 'individual') {
            $rules += [
                'country' => 'required|string|max:60',
                'city'    => 'nullable|string|max:60',
            ];
        } elseif ($accountType === 'practitioner') {
            $rules += [
                'profession'        => 'required|string|in:' . implode(',', array_keys(PractitionerProfile::professionOptions())),
                'specialty'         => 'nullable|string|max:120',
                'workplace_name'    => 'nullable|string|max:150',
                'workplace_country' => 'nullable|string|max:80',
            ];
        }

        $validated = $request->validate($rules);

        // Role derived server-side from the validated allowlist key — client cannot choose it.
        $role = self::ACCOUNT_TYPES[$validated['account_type']];

        $user = DB::transaction(function () use ($validated, $role) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => $validated['password'],
                'phone'     => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            $user->assignRole($role);

            if ($role === 'practitioner') {
                $user->practitionerProfile()->create([
                    'profession'        => $validated['profession'],
                    'specialty'         => $validated['specialty'] ?? null,
                    'workplace_name'    => $validated['workplace_name'] ?? null,
                    'workplace_country' => $validated['workplace_country'] ?? 'CM',
                ]);
            } else {
                $user->customerProfile()->create([
                    'facility_name' => $validated['facility_name'] ?? null,
                    'facility_type' => $validated['facility_type'] ?? null,
                    'country'       => $validated['country'],
                    'city'          => $validated['city'] ?? null,
                ]);
            }

            return $user;
        });


        $locale = $validated['locale'] ?? 'en';

        if ($role === 'practitioner') {
            \App\Contexts\Shared\Infrastructure\DomainMail::to($user->email)->queue(new PractitionerWelcome($user));
            $user->notify(new \App\Notifications\FeedEntry(
                'account.welcome',
                'Welcome to OPES',
                'Welcome to the OPES practitioner platform.',
                'sparkles',
                route('practitioner.dashboard', ['locale' => $locale]),
            ));
            return \App\Contexts\Shared\Application\WorkflowResult::payload(['user_id' => $user->id, 'account_type' => $validated['account_type'], 'locale' => $locale], 201);
        }

        \App\Contexts\Shared\Infrastructure\DomainMail::to($user->email)->queue(new WelcomeEmail($user));
        $user->notify(new \App\Notifications\FeedEntry(
            'account.welcome',
            'Welcome to OPES',
            'Welcome to OPES Health Systems.',
            'sparkles',
            route('customer.dashboard', ['locale' => $locale]),
        ));
        return \App\Contexts\Shared\Application\WorkflowResult::payload(['user_id' => $user->id, 'account_type' => $validated['account_type'], 'locale' => $locale], 201);
    }
}
