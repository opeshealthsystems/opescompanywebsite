<?php

namespace App\Http\Controllers;

use App\Models\HospitalOnboarding;
use App\Models\OnboardingStaff;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Public EMR demo onboarding intake. A clinic submits its full setup information and
 * the staff who will access the system; the OPES team receives it and creates each
 * account manually in the separate EMR SaaS.
 *
 * Note: patient records are deliberately NOT uploaded here (PHI). Per policy they are
 * sent later over a secure link; this form only captures their format and volume.
 */
class EmrOnboardingController extends Controller
{
    /** Fixed single-file document fields: request field => model column. */
    private const DOCUMENTS = [
        'logo'               => 'logo_path',
        'stamp'              => 'stamp_path',
        'letterhead'         => 'letterhead_path',
        'signed_agreement'   => 'signed_agreement_path',
        'service_price_list' => 'service_price_list_path',
        'drug_stock'         => 'drug_stock_path',
        'lab_test_list'      => 'lab_test_list_path',
        'pharmacy_licence'   => 'pharmacy_licence_path',
        'lab_licence'        => 'lab_licence_path',
    ];

    public function show(Request $request)
    {
        $products = Product::publicCatalogue()->sortBy('name');

        return view('pages.emr-onboarding', compact('products'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            // Facility
            'facility_name'        => 'required|string|max:150',
            'facility_type'        => 'nullable|string|max:40',
            'facility_size'        => 'nullable|string|max:20',
            'country'              => 'nullable|string|max:60',
            'city'                 => 'nullable|string|max:80',
            'address'              => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:30',
            'email'                => 'nullable|email|max:150',
            'website'              => 'nullable|string|max:150',
            // Person completing
            'contact_name'         => 'required|string|max:120',
            'contact_title'        => 'nullable|string|max:80',
            'contact_phone'        => 'nullable|string|max:30',
            'contact_email'        => 'required|email|max:150',
            'completed_by_role'    => 'nullable|string|max:80',
            'completer_authorised' => 'nullable|boolean',
            'data_sharing_consent' => 'accepted',
            // Owner / director
            'owner_name'           => 'nullable|string|max:120',
            'owner_title'          => 'nullable|string|max:80',
            'owner_phone'          => 'nullable|string|max:30',
            'owner_email'          => 'nullable|email|max:150',
            // Clinic identity
            'legal_name'           => 'nullable|string|max:160',
            'trading_name'         => 'nullable|string|max:160',
            'registration_number'  => 'nullable|string|max:80',
            'registration_date'    => 'nullable|date',
            'tax_number'           => 'nullable|string|max:60',
            'licence_number'       => 'nullable|string|max:80',
            'licence_authority'    => 'nullable|string|max:120',
            'gps_location'         => 'nullable|string|max:120',
            'opening_hours'        => 'nullable|string|max:160',
            'working_language'     => 'nullable|in:en,fr',
            // Services
            'services'             => 'nullable|array',
            'services.*'           => 'string|max:40',
            'departments'          => 'nullable|string|max:500',
            'beds'                 => 'nullable|integer|min:0|max:100000',
            'consulting_rooms'     => 'nullable|integer|min:0|max:100000',
            'patients_per_day'     => 'nullable|integer|min:0|max:1000000',
            'insurers'             => 'nullable|string|max:1000',
            'mobile_money'         => 'nullable|string|max:255',
            'vat_rate'             => 'nullable|string|max:20',
            'currency'             => 'nullable|string|max:10',
            // Setup
            'devices'              => 'nullable|string|max:1000',
            'connectivity_notes'   => 'nullable|string|max:1000',
            'email_domain_choice'  => 'nullable|in:own,ours',
            'email_domain'         => 'nullable|string|max:120',
            'current_software'     => 'nullable|string|max:1000',
            'training_attendees'   => 'nullable|string|max:1000',
            'preferred_start_date' => 'nullable|date',
            // Roles
            'system_admin'         => 'nullable|string|max:120',
            'records_viewers'      => 'nullable|string|max:1000',
            'approval_authority'   => 'nullable|string|max:1000',
            // Patient-data readiness (no file here)
            'patient_data_format'  => 'nullable|in:excel,pdf,paper,none',
            'patient_record_count' => 'nullable|integer|min:0|max:100000000',
            'patient_data_notes'   => 'nullable|string|max:1000',
            'notes'                => 'nullable|string|max:2000',
            // Documents
            'logo'               => 'nullable|image|max:4096',
            'stamp'              => 'nullable|image|max:4096',
            'letterhead'         => 'nullable|image|max:4096',
            'signed_agreement'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'service_price_list' => 'nullable|file|mimes:pdf,xls,xlsx,csv,doc,docx|max:10240',
            'drug_stock'         => 'nullable|file|mimes:pdf,xls,xlsx,csv|max:10240',
            'lab_test_list'      => 'nullable|file|mimes:pdf,xls,xlsx,csv|max:10240',
            'pharmacy_licence'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'lab_licence'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            // Repeaters
            'sites'              => 'nullable|array|max:50',
            'staff'              => 'nullable|array|max:200',
            'staff.*.signature'  => 'nullable|image|max:4096',
        ]);

        $documents = [];
        foreach (self::DOCUMENTS as $field => $column) {
            if ($path = $this->store($request->file($field))) {
                $documents[$column] = $path;
            }
        }

        $siteRows  = $this->siteRows($request);
        $staffRows = $this->staffRows($request);

        $onboarding = DB::transaction(function () use ($validated, $documents, $siteRows, $staffRows, $request) {
            $onboarding = HospitalOnboarding::create(array_merge($validated, $documents, [
                'completer_authorised' => $request->boolean('completer_authorised'),
                'data_sharing_consent' => $request->boolean('data_sharing_consent'),
                'locale'               => app()->getLocale(),
                'status'               => 'new',
                'ip_address'           => $request->ip(),
            ]));

            if ($siteRows !== []) {
                $onboarding->sites()->createMany($siteRows);
            }
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

    private function siteRows(Request $request): array
    {
        return collect($request->input('sites', []))
            ->filter(fn ($row) => is_array($row) && filled($row['name'] ?? null))
            ->map(fn ($row) => [
                'name'    => mb_substr((string) $row['name'], 0, 160),
                'address' => filled($row['address'] ?? null) ? mb_substr((string) $row['address'], 0, 255) : null,
            ])
            ->values()
            ->all();
    }

    /** Normalise staff rows: drop empty ones, whitelist options, store signatures. */
    private function staffRows(Request $request): array
    {
        $rows = [];

        foreach ((array) $request->input('staff', []) as $index => $row) {
            if (! is_array($row) || blank($row['full_name'] ?? null)) {
                continue;
            }

            $rows[] = [
                'full_name'            => mb_substr((string) $row['full_name'], 0, 120),
                'job_role'             => $this->pick($row['job_role'] ?? null, array_keys(OnboardingStaff::$jobRoles)),
                'specialty'            => $this->text($row['specialty'] ?? null, 120),
                'department'           => $this->text($row['department'] ?? null, 80),
                'site'                 => $this->text($row['site'] ?? null, 160),
                'phone'                => $this->text($row['phone'] ?? null, 30),
                'email'                => $this->text($row['email'] ?? null, 150),
                'access_level'         => $this->pick($row['access_level'] ?? null, array_keys(OnboardingStaff::$accessLevels)),
                'takes_tasks'          => in_array($this->pick($row['access_level'] ?? null, array_keys(OnboardingStaff::$accessLevels)), ['enter_data', 'approve', 'admin'], true),
                'licence_number'       => $this->text($row['licence_number'] ?? null, 80),
                'licence_issuing_body' => $this->text($row['licence_issuing_body'] ?? null, 120),
                'licence_expiry'       => $this->date($row['licence_expiry'] ?? null),
                'years_experience'     => isset($row['years_experience']) && is_numeric($row['years_experience']) ? (int) $row['years_experience'] : null,
                'working_hours'        => $this->text($row['working_hours'] ?? null, 160),
                'consultation_fee'     => $this->text($row['consultation_fee'] ?? null, 40),
                'signature_path'       => $this->store($request->file("staff.$index.signature")),
            ];
        }

        return $rows;
    }

    private function store(?UploadedFile $file): ?string
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }

        return $file->store('onboarding/' . now()->format('Y/m'), 'local');
    }

    private function text(mixed $value, int $max): ?string
    {
        return filled($value) ? mb_substr((string) $value, 0, $max) : null;
    }

    private function date(mixed $value): ?string
    {
        return filled($value) ? (strtotime((string) $value) ? date('Y-m-d', strtotime((string) $value)) : null) : null;
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
