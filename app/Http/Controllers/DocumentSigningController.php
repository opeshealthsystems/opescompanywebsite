<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentSigningController extends Controller
{
    public function show(string $token)
    {
        $document = Document::where('signature_token', $token)->firstOrFail();

        if (!$document->isSigningTokenValid()) {
            return view('documents.sign', ['document' => $document, 'expired' => true]);
        }

        return view('documents.sign', ['document' => $document, 'expired' => false]);
    }

    public function sign(Request $request, string $token)
    {
        $document = Document::where('signature_token', $token)->firstOrFail();

        abort_unless($document->isSigningTokenValid(), 410, 'This signing link has expired or is no longer valid.');

        $validated = $request->validate([
            'typed_name'  => 'required|string|max:150',
            'canvas_data' => 'nullable|string|max:100000',
        ]);

        $document->update([
            'status'                     => 'signed',
            'signed_at'                  => now(),
            'signed_by_name'             => $validated['typed_name'],
            'signed_ip'                  => $request->ip(),
            'signed_data'                => [
                'typed_name'  => $validated['typed_name'],
                'canvas_data' => $validated['canvas_data'] ?? null,
            ],
            'signature_token'            => null,
            'signature_token_expires_at' => null,
        ]);

        return redirect()->route('documents.sign.success', $document->reference_number);
    }
}
