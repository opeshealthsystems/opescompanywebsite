<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::where('status', 'active')
            ->whereIn('audience', ['customers', 'all'])
            ->get();

        $myResponseIds = SurveyResponse::where('user_id', auth()->id())
            ->whereIn('survey_id', $surveys->pluck('id'))
            ->whereNotNull('submitted_at')
            ->pluck('survey_id')
            ->toArray();

        return view('customer.surveys.index', compact('surveys', 'myResponseIds'));
    }

    public function show(Survey $survey)
    {
        abort_unless(in_array($survey->audience, ['customers', 'all']), 403);
        $survey->load('questions');

        $response = SurveyResponse::firstOrCreate(
            ['survey_id' => $survey->id, 'user_id' => auth()->id()]
        );

        $existingAnswers = $response->answers()->pluck('answer_text', 'question_id')->toArray();

        return view('customer.surveys.show', compact('survey', 'response', 'existingAnswers'));
    }

    public function submit(Request $request, Survey $survey)
    {
        abort_unless(in_array($survey->audience, ['customers', 'all']), 403);
        $survey->load('questions');

        $response = SurveyResponse::firstOrCreate(
            ['survey_id' => $survey->id, 'user_id' => auth()->id()]
        );

        if ($response->isSubmitted()) {
            return back()->with('error', 'You have already submitted this survey.');
        }

        foreach ($survey->questions as $question) {
            $key = "q_{$question->id}";
            $value = $request->input($key);

            if ($question->is_required && empty($value)) {
                return back()->withErrors(["q_{$question->id}" => 'This question is required.'])->withInput();
            }

            $answerData = ['response_id' => $response->id, 'question_id' => $question->id];
            match ($question->type) {
                'rating'          => $answerData['answer_rating'] = (int) $value,
                'multiple_choice' => $answerData['answer_choice'] = $value,
                'yes_no'          => $answerData['answer_bool']   = ($value === 'yes'),
                default           => $answerData['answer_text']   = $value,
            };

            SurveyAnswer::updateOrCreate(
                ['response_id' => $response->id, 'question_id' => $question->id],
                $answerData
            );
        }

        $response->update(['submitted_at' => now()]);

        return redirect()
            ->route('customer.surveys', ['locale' => app()->getLocale()])
            ->with('success', 'Survey submitted. Thank you for your feedback!');
    }
}
