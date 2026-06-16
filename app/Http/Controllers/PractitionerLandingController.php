<?php
namespace App\Http\Controllers;

use App\Models\PractitionerProfile;
use Illuminate\Http\Request;

class PractitionerLandingController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.practitioners', [
            'professions' => PractitionerProfile::professionOptions(),
        ]);
    }
}
