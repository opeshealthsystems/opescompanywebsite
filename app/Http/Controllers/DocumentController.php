<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function pdf(Request $request, Document $document)
    {
        $user = $request->user();
        $canAccess = $user->hasAnyRole(['super_admin', 'admin', 'support'])
            || ($document->addressee_user_id && $document->addressee_user_id === $user->id);

        abort_unless($canAccess, 403);

        $pdf = Pdf::loadView('documents.pdf', compact('document'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($document->reference_number . '.pdf');
    }

    public function preview(Request $request, Document $document)
    {
        $user = $request->user();
        $canAccess = $user->hasAnyRole(['super_admin', 'admin', 'support'])
            || ($document->addressee_user_id && $document->addressee_user_id === $user->id);

        abort_unless($canAccess, 403);

        return view('documents.pdf', compact('document'));
    }
}
