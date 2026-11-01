<?php

namespace App\Contexts\Learning\Application\Queries;

use App\Models\ValidationCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
class PractitionerCertificateQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $certificates = $workflowInput->collect($user->courseCertificates()->with('course')->latest('issued_at'));
        $validationCertificates = $workflowInput->collect($user->validationCertificates()->with('cohortMember.cohort')->latest('issued_at'));
        $councilMembership = $user->advisoryCouncilMembership()->where('status', 'active')->first();
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_certificatecontroller_index_1', compact('certificates', 'validationCertificates', 'councilMembership'));
    }
    public function downloadValidation(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\ValidationCertificate $certificate): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($certificate->cohortMember?->user_id === \App\Contexts\Shared\Application\CurrentWorkflow::id(), 403);
        $certificate->load('cohortMember.user', 'cohortMember.cohort');
        return \App\Contexts\Shared\Application\WorkflowResult::download(\App\Contexts\Shared\Infrastructure\PdfRenderer::document('pdf.validation-certificate', ['certificate' => $certificate])->setPaper('a4', 'landscape')->output(), $certificate->certificate_number . '.pdf');
    }
}
