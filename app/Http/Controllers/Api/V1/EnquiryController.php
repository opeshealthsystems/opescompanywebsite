<?php
namespace App\Http\Controllers\Api\V1;

use App\Contexts\Enquiries\Application\SubmitEnquiry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EnquiryController extends Controller
{
    public function __invoke(Request $request, SubmitEnquiry $submit)
    {
        abort_unless($request->isJson(), 415);
        if (array_diff(array_keys($request->all()), [...array_keys(SubmitEnquiry::RULES), 'locale', 'quote_option'])) {
            throw ValidationException::withMessages(['body' => ['Unknown fields are not accepted.']]);
        }
        $fields = $request->validate(SubmitEnquiry::RULES + [
            'locale' => 'sometimes|required|string|in:en,fr',
            'quote_option' => ['sometimes', 'required', 'string', Rule::in(\App\Contexts\Enquiries\Application\PricingQuoteSelection::keys())],
        ]);
        $quoteOption = $fields['quote_option'] ?? null;
        unset($fields['quote_option']);
        $key = $request->header('Idempotency-Key');
        if ($key !== null) {
            validator(['idempotency_key' => $key], ['idempotency_key' => ['required', 'string', 'min:16', 'max:128', 'regex:/\A[A-Za-z0-9._:-]+\z/']])->validate();
        }
        $result = $submit->handle($fields, $fields['locale'] ?? 'en', $request->ip(), $key, $quoteOption);
        return response()->json($result['response'], 201, $key === null ? [] : ['Idempotency-Replayed' => $result['replayed'] ? 'true' : 'false']);
    }
}
