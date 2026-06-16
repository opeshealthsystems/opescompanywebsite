<?php
namespace App\Http\Controllers;

use App\Models\CourseCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificatePdfController extends Controller
{
    public function download(CourseCertificate $certificate)
    {
        $user = auth()->user();
        $isOwner = $certificate->user_id === $user->id;
        $isAdmin = $user->hasAnyRole(['super_admin', 'admin']);
        abort_unless($isOwner || $isAdmin, 403);

        $certificate->load('user', 'course');

        $pdf = Pdf::loadView('certificates.course', ['certificate' => $certificate])
            ->setPaper('a4', 'landscape');

        if (!$certificate->pdf_path) {
            $path = 'certificates/' . $certificate->certificate_number . '.pdf';
            Storage::put($path, $pdf->output());
            $certificate->update(['pdf_path' => $path]);
        }

        return $pdf->download('certificate-' . $certificate->certificate_number . '.pdf');
    }
}
