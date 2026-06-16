<?php
namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = auth()->user()->courseCertificates()->with('course')->latest('issued_at')->get();
        return view('practitioner.certificates.index', compact('certificates'));
    }
}
