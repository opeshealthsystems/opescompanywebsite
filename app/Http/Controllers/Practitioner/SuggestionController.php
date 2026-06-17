<?php
namespace App\Http\Controllers\Practitioner;

use App\Filament\Resources\SuggestionResource;
use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Support\AdminNotifier;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function index()
    {
        $suggestions = auth()->user()->suggestions()->latest()->get();
        return view('practitioner.suggestions.index', compact('suggestions'));
    }

    public function create()
    {
        $categoryOptions = Suggestion::categoryOptions();
        return view('practitioner.suggestions.create', compact('categoryOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:200',
            'category' => 'required|string|in:' . implode(',', array_keys(Suggestion::categoryOptions())),
            'body'     => 'required|string|min:20',
        ]);

        $suggestion = auth()->user()->suggestions()->create($data);

        AdminNotifier::notify(
            'New suggestion submitted',
            auth()->user()->name . ' submitted: ' . $suggestion->title,
            SuggestionResource::getUrl('view', ['record' => $suggestion]),
        );

        return redirect()
            ->route('practitioner.suggestions', ['locale' => app()->getLocale()])
            ->with('success', 'Your suggestion has been submitted. Thank you!');
    }
}
