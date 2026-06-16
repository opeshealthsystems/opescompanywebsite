<?php
namespace App\Http\Controllers;

use App\Models\PractitionerFinding;
use App\Models\PractitionerProfile;
use Illuminate\Http\Request;

class PractitionerLandingController extends Controller
{
    public function index(Request $request)
    {
        $testimonials = PractitionerProfile::query()
            ->where('is_verified', true)
            ->whereNotNull('opes_testimonial')
            ->where('opes_testimonial', '!=', '')
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        $publishedFindings = PractitionerFinding::query()
            ->where('is_published', true)
            ->with('practitioner')
            ->latest()
            ->take(6)
            ->get();

        return view('pages.practitioners', [
            'professions' => PractitionerProfile::professionOptions(),
            'testimonials' => $testimonials,
            'publishedFindings' => $publishedFindings,
        ]);
    }
}
