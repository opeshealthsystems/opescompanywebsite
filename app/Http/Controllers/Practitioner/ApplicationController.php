<?php
namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Models\PractitionerApplication;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = PractitionerApplication::where('practitioner_id', auth()->id())
            ->with('program')
            ->latest()
            ->get();

        return view('practitioner.applications.index', compact('applications'));
    }

    public function show($locale, PractitionerApplication $application)
    {
        abort_unless($application->practitioner_id === auth()->id(), 403);
        $application->load('program', 'findings');
        return view('practitioner.applications.show', compact('application'));
    }
}
