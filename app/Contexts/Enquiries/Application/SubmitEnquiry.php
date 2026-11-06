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
        $quoteSelection = PricingQuoteSelection::find($quoteOption, $locale);
        if ($quoteOption !== null && $quoteSelection === null) {
            throw \Illuminate\Validation\ValidationException::withMessages(['quote_option' => ['The selected quote option is invalid.']]);
        }
        if ($quoteOption !== null && is_array($payload['integration_application'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['quote_option' => ['Quote requests and API access requests must be submitted separately.']]);
        }
        if ($quoteSelection !== null) {
            $payload['product_slug'] = $quoteSelection['product_slug'];
            $payload['products'] = implode(' — ', [$quoteSelection['product'], $quoteSelection['option'], $quoteSelection['price']]);
        }
        $fingerprint = hash_hmac('sha256', json_encode([$payload, $locale], JSON_THROW_ON_ERROR), (string) config('app.key'));
        $keyHash = $idempotencyKey === null ? null : hash('sha256', 'POST:/api/v1/enquiries:'.$idempotencyKey);
        $prepared = \Illuminate\Support\Facades\DB::transaction(function () use ($payload, $locale, $ip, $keyHash, $fingerprint, $quoteSelection) {
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
                'source' => is_array($payload['integration_application']) ? 'integration-request' : (($quoteSelection !== null || !empty($payload['product_slug'])) ? 'product-page' : 'contact'), 'locale' => $locale, 'ip_address' => $ip, 'status' => 'new',
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
