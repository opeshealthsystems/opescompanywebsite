<?php
namespace App\Http\Controllers\Api\V1;

use App\Contexts\Enquiries\Application\SubmitEnquiry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EnquiryController extends Controller
{
    public function __invoke(Request $request, SubmitEnquiry $submit)
    {
        abort_unless($request->isJson(), 415);
        if (array_diff(array_keys($request->all()), [...array_keys(SubmitEnquiry::RULES), 'locale'])) {
            throw ValidationException::withMessages(['body' => ['Unknown fields are not accepted.']]);
        }
        $fields = $request->validate(SubmitEnquiry::RULES + ['locale' => 'sometimes|required|string|in:en,fr']);
        $key = $request->header('Idempotency-Key');
        if ($key !== null) {
            validator(['idempotency_key' => $key], ['idempotency_key' => ['required', 'string', 'min:16', 'max:128', 'regex:/\A[A-Za-z0-9._:-]+\z/']])->validate();
        }
        $result = $submit->handle($fields, $fields['locale'] ?? 'en', $request->ip(), $key);
        return response()->json($result['response'], 201, $key === null ? [] : ['Idempotency-Replayed' => $result['replayed'] ? 'true' : 'false']);
    }
}
