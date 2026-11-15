<?php

namespace App\Http\Controllers;

use App\Models\HospitalOnboarding;
use App\Models\OnboardingStaff;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Public EMR demo onboarding intake. A hospital submits its facility details and the
 * full list of staff who will access the system; the OPES team receives it and creates
 * each account manually in the separate EMR SaaS.
 */
class EmrOnboardingController extends Controller
{
    public function show(Request $request)
    {
        $products = Product::publicCatalogue()->sortBy('name');

        return view('pages.emr-onboarding', compact('products'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'facility_name'        => 'required|string|max:150',
            'facility_type'        => 'nullable|string|max:40',
            'facility_size'        => 'nullable|string|max:20',
            'country'              => 'nullable|string|max:60',
            'city'                 => 'nullable|string|max:80',
            'address'              => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:30',
            'email'                => 'nullable|email|max:150',
            'website'              => 'nullable|string|max:150',
            'registration_number'  => 'nullable|string|max:80',
            'contact_name'         => 'required|string|max:120',
            'contact_title'        => 'nullable|string|max:80',
            'contact_phone'        => 'nullable|string|max:30',
            'contact_email'        => 'required|email|max:150',
            'products'             => 'nullable|array',
            'products.*'           => 'string|max:60',
            'departments'          => 'nullable|string|max:500',
            'preferred_start_date' => 'nullable|date',
            'notes'                => 'nullable|string|max:2000',
            'staff'                => 'nullable|array|max:100',
        ]);

        $staffRows = $this->staffRows($request);

        $onboarding = DB::transaction(function () use ($validated, $staffRows, $request) {
            $onboarding = HospitalOnboarding::create(array_merge($validated, [
                'locale'     => app()->getLocale(),
                'status'     => 'new',
                'ip_address' => $request->ip(),
            ]));

            if ($staffRows !== []) {
                $onboarding->staff()->createMany($staffRows);
            }

            return $onboarding;
        });

        $this->notifyTeam($onboarding);

        return redirect()
            ->route('emr-onboarding', ['locale' => app()->getLocale()])
            ->with('success', true);
    }

    /**
     * Normalise the submitted staff repeater: drop empty rows, keep only known fields,
     * and coerce the "takes tasks" checkbox to a boolean.
     */
    private function staffRows(Request $request): array
    {
        return collect($request->input('staff', []))
            ->filter(fn ($row) => is_array($row) && filled($row['full_name'] ?? null))
            ->map(fn ($row) => [
                'full_name'    => mb_substr((string) $row['full_name'], 0, 120),
                'job_role'     => $this->pick($row['job_role'] ?? null, array_keys(OnboardingStaff::$jobRoles)),
                'department'   => filled($row['department'] ?? null) ? mb_substr((string) $row['department'], 0, 80) : null,
                'phone'        => filled($row['phone'] ?? null) ? mb_substr((string) $row['phone'], 0, 30) : null,
                'email'        => filled($row['email'] ?? null) ? mb_substr((string) $row['email'], 0, 150) : null,
                'access_level' => $this->pick($row['access_level'] ?? null, array_keys(OnboardingStaff::$accessLevels)),
                'takes_tasks'  => ! empty($row['takes_tasks']),
            ])
            ->values()
            ->all();
    }

    /** Only accept a value that is one of the allowed option keys. */
    private function pick(mixed $value, array $allowed): ?string
    {
        return in_array($value, $allowed, true) ? $value : null;
    }

    private function notifyTeam(HospitalOnboarding $onboarding): void
    {
        $admins = User::role(['admin', 'super_admin'])->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('New EMR onboarding: ' . $onboarding->facility_name)
            ->body($onboarding->contact_name . ' · ' . $onboarding->contact_email
                . ' · ' . $onboarding->staff()->count() . ' staff')
            ->icon('heroicon-o-building-office-2')
            ->iconColor('info')
            ->sendToDatabase($admins);
    }
}
