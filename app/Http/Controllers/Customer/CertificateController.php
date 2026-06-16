<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = auth()->user()->courseCertificates()->with('course')->latest('issued_at')->get();
        return view('customer.certificates.index', compact('certificates'));
    }
}
