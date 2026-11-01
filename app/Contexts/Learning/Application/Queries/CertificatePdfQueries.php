<?php

namespace App\Contexts\Learning\Application\Queries;

use App\Models\CourseCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
class CertificatePdfQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function download(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, \App\Models\CourseCertificate $certificate): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $isOwner = $certificate->user_id === $user->id;
        $isAdmin = $user->hasAnyRole(['super_admin', 'admin']);
        abort_unless($isOwner || $isAdmin, 403);
        $certificate->load('user', 'course');
        $pdf = \App\Contexts\Shared\Infrastructure\PdfRenderer::document('certificates.course', ['certificate' => $certificate])->setPaper('a4', 'landscape');
        if (!$certificate->pdf_path) {
            $path = 'certificates/' . $certificate->certificate_number . '.pdf';
            \Illuminate\Support\Facades\Storage::put($path, $pdf->output());
            $certificate->update(['pdf_path' => $path]);
        }
        return \App\Contexts\Shared\Application\WorkflowResult::download($pdf->output(), 'certificate-' . $certificate->certificate_number . '.pdf');
    }
}
