<?php

namespace App\Contexts\Documents\Application\Commands;

use App\Models\Document;
class DocumentSigningCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function sign(\App\Contexts\Shared\Application\WorkflowInput $request, string $token): \App\Contexts\Shared\Application\WorkflowResult
    {
        // If token no longer exists (already signed + cleared), show 410 rather than 404
        $document = \App\Models\Document::where('signature_token', $token)->lockForUpdate()->first();
        if (!$document) {
            abort(410, 'This signing link has already been used or does not exist.');
        }
        abort_unless($document->isSigningTokenValid(), 410, 'This signing link has expired or is no longer valid.');
        $validated = $request->validate(['typed_name' => 'required|string|max:150', 'canvas_data' => 'nullable|string|max:100000']);
        $document->update(['status' => 'signed', 'signed_at' => now(), 'signed_by_name' => $validated['typed_name'], 'signed_ip' => $request->ip(), 'signed_data' => ['typed_name' => $validated['typed_name'], 'canvas_data' => $validated['canvas_data'] ?? null], 'signature_token' => null, 'signature_token_expires_at' => null]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('documentsigningcontroller_sign_1', $document->reference_number);
    }
}
