<?php
namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Models\ValidationCertificate;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $certificates = $user->courseCertificates()->with('course')->latest('issued_at')->get();
        $validationCertificates = $user->validationCertificates()->with('cohortMember.cohort')->latest('issued_at')->get();
        $councilMembership = $user->advisoryCouncilMembership()->where('status', 'active')->first();

        return view('practitioner.certificates.index', compact('certificates', 'validationCertificates', 'councilMembership'));
    }

    public function downloadValidation($locale, ValidationCertificate $certificate)
    {
        abort_unless($certificate->cohortMember?->user_id === auth()->id(), 403);

        $certificate->load('cohortMember.user', 'cohortMember.cohort');

        return Pdf::loadView('pdf.validation-certificate', ['certificate' => $certificate])
            ->setPaper('a4', 'landscape')
            ->download($certificate->certificate_number . '.pdf');
    }
}
