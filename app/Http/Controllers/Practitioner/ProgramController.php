<?php
namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Mail\PractitionerApplicationReceived;
use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = PractitionerProgram::where('status', 'open')
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->get();

        $myApplicationProgramIds = PractitionerApplication::where('practitioner_id', auth()->id())
            ->pluck('program_id')
            ->toArray();

        return view('practitioner.programs.index', compact('programs', 'myApplicationProgramIds'));
    }

    public function show(PractitionerProgram $program)
    {
        $program->loadCount('applications');
        $myApplication = PractitionerApplication::where('practitioner_id', auth()->id())
            ->where('program_id', $program->id)
            ->first();

        return view('practitioner.programs.show', compact('program', 'myApplication'));
    }

    public function apply(Request $request, $locale, PractitionerProgram $program)
    {
        abort_unless($program->isOpen(), 403, 'This programme is not accepting applications.');
        abort_if($program->isFull(), 403, 'This programme has reached its maximum participants.');

        $exists = PractitionerApplication::where('practitioner_id', auth()->id())
            ->where('program_id', $program->id)
            ->exists();
        abort_if($exists, 422, 'You have already applied to this programme.');

        $validated = $request->validate([
            'motivation' => 'nullable|string|max:2000',
        ]);

        $application = PractitionerApplication::create([
            'practitioner_id' => auth()->id(),
            'program_id'      => $program->id,
            'motivation'      => $validated['motivation'] ?? null,
            'status'          => 'pending',
        ]);

        Mail::to(auth()->user()->email)->queue(new PractitionerApplicationReceived($application));

        return redirect()
            ->route('practitioner.applications', ['locale' => app()->getLocale()])
            ->with('success', 'Your application has been submitted successfully.');
    }
}
