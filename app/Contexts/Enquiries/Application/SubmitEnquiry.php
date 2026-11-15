<?php
namespace App\Contexts\Enquiries\Application;

use App\Models\Lead;
use App\Models\User;
use App\Services\WebsiteFormMailer;
use Filament\Notifications\Notification;

class SubmitEnquiry
{
    public const RULES = [
        'name' => 'required|string|max:100', 'email' => 'required|email|max:150',
        'phone' => 'nullable|string|max:30', 'facility_type' => 'nullable|string|max:60',
        'products' => 'nullable|string|max:255', 'message' => 'nullable|string|max:2000',
        'product_slug' => 'nullable|string|max:60',
        'integration_application' => 'sometimes|array:application_name,access_type,redirect_uri,requested_scopes,use_case',
        'integration_application.application_name' => 'required_with:integration_application|string|max:100',
        'integration_application.access_type' => 'required_with:integration_application|in:machine,delegated,both',
        'integration_application.redirect_uri' => 'nullable|string|max:255',
        'integration_application.requested_scopes' => 'sometimes|array|max:20',
        'integration_application.requested_scopes.*' => 'required|string|distinct|max:80',
        'integration_application.use_case' => 'required_with:integration_application|string|min:10|max:1200',
        'participation' => 'sometimes|array:program,organization_name,organization_type,country,city,area,proposal,readiness,data_authority,data_status,data_period,contact_permission,no_sensitive_data',
        'participation.program' => 'required_with:participation|in:research,data-license',
        'participation.organization_name' => 'required_with:participation|string|max:150',
        'participation.organization_type' => 'required_with:participation|in:hospital,clinic,pharmacy,pharmaceutical,lab,university,public-health,technology-company,government,ngo,other',
        'participation.country' => 'required_with:participation|string|max:80',
        'participation.city' => 'nullable|string|max:80',
        'participation.area' => 'required_with:participation|in:medical-hardware,health-software,interoperability,data-quality,clinical-support,telemedicine-triage,pharmacy-medicines,laboratory-diagnostics,care-pathways,public-health,other',
        'participation.proposal' => 'required_with:participation|string|min:20|max:700',
        'participation.readiness' => 'nullable|in:exploring,protocol-planned,pilot-sought,active-study',
        'participation.data_authority' => 'required_if:participation.program,data-license|in:authorized,confirming,need-guidance',
        'participation.data_status' => 'nullable|in:aggregated,deidentified,not-assessed,unsure',
        'participation.data_period' => 'nullable|string|max:80',
        'participation.contact_permission' => 'required_with:participation|accepted',
        'participation.no_sensitive_data' => 'required_with:participation|accepted',
    ];

    public function __construct(private WebsiteFormMailer $mailer) {}

    public function handle(array $fields, string $locale, ?string $ip, ?string $idempotencyKey = null, ?string $quoteOption = null): array
    {
        $payload = array_replace(array_fill_keys(array_keys(self::RULES), null), array_intersect_key($fields, self::RULES));
        if (is_array($payload['integration_application'])) {
            $application = $payload['integration_application'];
            $machineScopes = ['catalogue:read', 'integration:read'];
            $delegatedScopes = [...$machineScopes, ...array_keys(\App\Contexts\ApiIdentity\Application\BusinessScopes::definitions())];
            $allowedScopes = $application['access_type'] === 'machine' ? $machineScopes : $delegatedScopes;
            $requestedScopes = array_values(array_unique($application['requested_scopes'] ?? []));
            if (array_diff($requestedScopes, $allowedScopes)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['integration_application.requested_scopes' => ['One or more requested scopes are not available.']]);
            }
            $redirectUri = $application['redirect_uri'] ?? null;
            if (in_array($application['access_type'], ['delegated', 'both'], true)) {
                $nativeCallback = $redirectUri === 'opeshealth://oauth/callback';
                $parts = is_string($redirectUri) ? parse_url($redirectUri) : false;
                $secureHttpsCallback = is_array($parts) && ($parts['scheme'] ?? null) === 'https'
                    && !empty($parts['host']) && !isset($parts['user'], $parts['pass'], $parts['fragment']);
                if (!$nativeCallback && !$secureHttpsCallback) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['integration_application.redirect_uri' => ['Delegated access requires an HTTPS callback or the reserved OPES Health native callback.']]);
                }
            } elseif ($redirectUri !== null) {
                throw \Illuminate\Validation\ValidationException::withMessages(['integration_application.redirect_uri' => ['Machine-only access does not use a redirect URI.']]);
            }
            $payload['message'] = trim($application['use_case']);
            $payload['products'] = 'API integration access: '.$application['access_type'];
            $payload['facility_type'] = 'API integration';
        }
        if (is_array($payload['participation'])) {
            $application = $payload['participation'];
            if (is_array($payload['integration_application'])) {
                throw \Illuminate\Validation\ValidationException::withMessages(['participation' => ['Submit a participation proposal separately from an API access request.']]);
            }
            $isDataLicense = $application['program'] === 'data-license';
            $areaLabels = [
                'medical-hardware' => 'Medical hardware and connected devices', 'health-software' => 'Health software and care workflows',
                'interoperability' => 'Interoperability and health data exchange', 'data-quality' => 'Data quality and analytics',
                'clinical-support' => 'Clinical decision support', 'telemedicine-triage' => 'Telemedicine and triage',
                'pharmacy-medicines' => 'Pharmacy and medicine availability', 'laboratory-diagnostics' => 'Laboratory and diagnostics',
                'care-pathways' => 'Care pathways and service use', 'public-health' => 'Public health', 'other' => 'Other health technology area',
            ];
            $organizationLabels = [
                'hospital' => 'Hospital or teaching hospital', 'clinic' => 'Clinic or care provider', 'pharmacy' => 'Pharmacy',
                'pharmaceutical' => 'Pharmaceutical company or manufacturer', 'lab' => 'Diagnostic or research laboratory',
                'university' => 'University or teaching institution', 'public-health' => 'Public health institution',
                'technology-company' => 'Health technology company', 'government' => 'Government institution', 'ngo' => 'NGO', 'other' => 'Other institution',
            ];
            $facilityTypes = ['hospital' => 'hospital', 'clinic' => 'clinic', 'pharmacy' => 'pharmacy', 'pharmaceutical' => 'other', 'lab' => 'laboratory', 'university' => 'other', 'public-health' => 'government', 'technology-company' => 'other', 'government' => 'government', 'ngo' => 'ngo', 'other' => 'other'];
            $readinessLabels = ['exploring' => 'Exploring a question', 'protocol-planned' => 'Protocol being planned', 'pilot-sought' => 'Seeking an evaluation or pilot', 'active-study' => 'Study already active'];
            $dataStatusLabels = ['aggregated' => 'Aggregated data', 'deidentified' => 'De-identified data', 'not-assessed' => 'Not yet assessed', 'unsure' => 'Not sure'];
            $payload['facility_type'] = $facilityTypes[$application['organization_type']] ?? 'other';
            $payload['products'] = ($isDataLicense ? 'Health data licensing — ' : 'Research participation — ').($areaLabels[$application['area']] ?? 'Health research');
            $details = [
                'Participation' => $isDataLicense ? 'De-identified health data licensing discussion' : 'Health technology research collaboration',
                'Institution' => $application['organization_name'],
                'Institution type' => $organizationLabels[$application['organization_type']] ?? $application['organization_type'],
                'Location' => trim(($application['city'] ?? '').(($application['city'] ?? '') !== '' ? ', ' : '').$application['country'], ', '),
                'Primary area' => $areaLabels[$application['area']] ?? $application['area'],
                'Proposal' => $application['proposal'],
                'Permission to follow up' => 'Confirmed',
                'Sensitive data acknowledgement' => 'Confirmed: no patient records, identifiers or data extracts were submitted.',
            ];
            if (!$isDataLicense && !empty($application['readiness'])) $details['Research stage'] = $readinessLabels[$application['readiness']] ?? $application['readiness'];
            if ($isDataLicense) {
                $authorityLabels = ['authorized' => 'Institution believes it can authorize the proposed use; subject to review', 'confirming' => 'Institution is confirming authority', 'need-guidance' => 'Institution requests a discussion about authority'];
                $details['Data authority'] = $authorityLabels[$application['data_authority']] ?? $application['data_authority'];
                if (!empty($application['data_status'])) $details['Current data preparation'] = $dataStatusLabels[$application['data_status']] ?? $application['data_status'];
                if (!empty($application['data_period'])) $details['Potential time period'] = $application['data_period'];
            }
            $payload['message'] = implode("\n", array_map(fn ($label, $value) => $label.': '.$value, array_keys($details), $details));
        }
        $quoteSelection = PricingQuoteSelection::find($quoteOption, $locale);
        if ($quoteOption !== null && $quoteSelection === null) {
            throw \Illuminate\Validation\ValidationException::withMessages(['quote_option' => ['The selected quote option is invalid.']]);
        }
        if ($quoteOption !== null && is_array($payload['integration_application'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['quote_option' => ['Quote requests and API access requests must be submitted separately.']]);
        }
        if ($quoteOption !== null && is_array($payload['participation'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['quote_option' => ['Quote requests and participation proposals must be submitted separately.']]);
        }
        if ($quoteSelection !== null) {
            $payload['product_slug'] = $quoteSelection['product_slug'];
            $payload['products'] = implode(' — ', [$quoteSelection['product'], $quoteSelection['option'], $quoteSelection['price']]);
        }
        $source = is_array($payload['participation'])
            ? ($payload['participation']['program'] === 'data-license' ? 'data-partnership' : 'research-participation')
            : (is_array($payload['integration_application']) ? 'integration-request' : (($quoteSelection !== null || !empty($payload['product_slug'])) ? 'product-page' : 'contact'));
        unset($payload['participation']);
        $fingerprint = hash_hmac('sha256', json_encode([$payload, $locale], JSON_THROW_ON_ERROR), (string) config('app.key'));
        $keyHash = $idempotencyKey === null ? null : hash('sha256', 'POST:/api/v1/enquiries:'.$idempotencyKey);
        $prepared = \Illuminate\Support\Facades\DB::transaction(function () use ($payload, $locale, $ip, $keyHash, $fingerprint, $quoteSelection, $source) {
            $receipt = null;
            if ($keyHash !== null) {
                \App\Models\ApiIdempotencyRecord::insertOrIgnore([
                    'key_hash' => $keyHash, 'fingerprint' => $fingerprint, 'expires_at' => now()->addDay(), 'created_at' => now(), 'updated_at' => now(),
                ]);
                $receipt = \App\Models\ApiIdempotencyRecord::where('key_hash', $keyHash)->lockForUpdate()->firstOrFail();
                if ($receipt->expires_at->isPast()) {
                    $receipt->delete();
                    $receipt = \App\Models\ApiIdempotencyRecord::create([
                        'key_hash' => $keyHash, 'fingerprint' => $fingerprint, 'expires_at' => now()->addDay(),
                    ]);
                }
                if (!hash_equals($receipt->fingerprint, $fingerprint)) {
                    throw new \Symfony\Component\HttpKernel\Exception\HttpException(409, 'Idempotency key payload mismatch');
                }
                if ($receipt->response !== null) return ['response' => $receipt->response, 'replayed' => true];
                if ($receipt->lead_id !== null) {
                    if ($receipt->created_at->greaterThan(now()->subMinutes(2))) {
                        throw new \Symfony\Component\HttpKernel\Exception\HttpException(409, 'Idempotent request is still processing', null, ['Retry-After' => '2']);
                    }
                    $sent = \App\Models\WebsiteFormDelivery::where('submission_type', Lead::class)->where('submission_id', $receipt->lead_id)->where('status', 'sent')->exists();
                    $receipt->response = ['data' => ['status' => 'recorded', 'delivery_status' => $sent ? 'sent' : 'pending']];
                    $receipt->save();
                    return ['response' => $receipt->response, 'replayed' => true];
                }
            }
            $lead = Lead::create(array_merge($payload, [
                'source' => $source, 'locale' => $locale, 'ip_address' => $ip, 'status' => 'new',
            ]));
            $this->mailer->ensure($lead);
            if ($receipt) { $receipt->lead_id = $lead->id; $receipt->save(); }
            return ['lead' => $lead, 'receipt_id' => $receipt?->id];
        }, 3);
        if (isset($prepared['response'])) return $prepared;
        $lead = $prepared['lead'];
        $delivery = $this->mailer->deliver($lead);
        $receiptDelivery = $quoteSelection !== null ? $this->mailer->deliverQuoteReceipt($lead) : null;
        // A staff notification failure must not make the caller repeat an already committed submission.
        try {
            $admins = User::role('admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::make()->title('New lead: '.$lead->name)
                    ->body($lead->email.($lead->facility_type ? ' - '.$lead->facility_type : ''))
                    ->icon('heroicon-o-user-plus')->iconColor('success')->sendToDatabase($admins);
            }
        } catch (\Throwable $error) { report($error); }
        $responseData = ['status' => 'recorded', 'delivery_status' => in_array($delivery, ['sent', 'already-sent'], true) ? 'sent' : 'pending'];
        if ($quoteSelection !== null) {
            $responseData['receipt_delivery_status'] = in_array($receiptDelivery, ['sent', 'already-sent'], true) ? 'sent' : 'pending';
        }
        $response = ['data' => $responseData];
        if ($prepared['receipt_id'] !== null) {
            $response = \Illuminate\Support\Facades\DB::transaction(function () use ($prepared, $response) {
                $receipt = \App\Models\ApiIdempotencyRecord::whereKey($prepared['receipt_id'])->lockForUpdate()->firstOrFail();
                if ($receipt->response !== null) return $receipt->response;
                $receipt->response = $response; $receipt->save();
                return $response;
            });
        }
        return ['response' => $response, 'replayed' => false, 'receipt_status' => $receiptDelivery];
    }
}
