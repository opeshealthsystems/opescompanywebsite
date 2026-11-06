<?php

namespace App\Contexts\Learning\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Course;
use App\Models\CourseAssessment;
use App\Models\CourseAssessmentAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class CourseAssessmentQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
        return $user;
    }

    private function practitioner(WorkflowInput $input): User
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasRole('practitioner'), 403);
        return $user;
    }

    private function assertEnrolled(User $user, Course $course): void
    {
        abort_unless($course->is_active && $user->courseEnrollments()->where('course_id', $course->id)->where('status', '!=', 'dropped')->exists(), 403,
            'Enroll in this active course to access assessments.');
    }

    private function adminProjection(CourseAssessment $assessment): array
    {
        return ['id' => (int) $assessment->id, 'course_id' => (int) $assessment->course_id, 'title' => $assessment->title,
            'instructions' => $assessment->instructions, 'pass_mark' => (int) $assessment->pass_mark,
            'attempt_limit' => $assessment->attempt_limit, 'duration_minutes' => $assessment->duration_minutes,
            'is_published' => (bool) $assessment->is_published, 'questions' => $assessment->questions ?? [],
            'attempts_count' => (int) ($assessment->attempts_count ?? 0), 'created_at' => $assessment->created_at?->toISOString(),
            'updated_at' => $assessment->updated_at?->toISOString()];
    }

    private function learnerProjection(CourseAssessment $assessment): array
    {
        return ['id' => (int) $assessment->id, 'course_id' => (int) $assessment->course_id, 'course_slug' => $assessment->course?->slug,
            'title' => $assessment->title, 'instructions' => $assessment->instructions, 'pass_mark' => (int) $assessment->pass_mark,
            'attempt_limit' => $assessment->attempt_limit, 'duration_minutes' => $assessment->duration_minutes,
            'questions' => array_map(fn ($question) => ['id' => $question['id'], 'prompt' => $question['prompt'],
                'choices' => $question['choices'], 'points' => $question['points'] ?? 1], $assessment->questions ?? [])];
    }

    public function adminQuery(WorkflowInput $input, Course $course): Builder
    {
        $this->admin($input);
        return CourseAssessment::query()->where('course_id', $course->id)->withCount('attempts')->orderByDesc('id');
    }

    public function adminList(WorkflowInput $input, Course $course): WorkflowResult
    {
        $query = $this->adminQuery($input, $course);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn ($record) => $this->adminProjection($record))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function adminShow(WorkflowInput $input, Course $course, CourseAssessment $assessment): WorkflowResult
    {
        $this->admin($input);
        abort_unless((int) $assessment->course_id === (int) $course->id, 404);
        return WorkflowResult::payload(['assessment' => $this->adminProjection($assessment->loadCount('attempts'))]);
    }

    public function practitionerList(WorkflowInput $input, Course $course): WorkflowResult
    {
        $user = $this->practitioner($input);
        $this->assertEnrolled($user, $course);
        $assessments = CourseAssessment::query()->where('course_id', $course->id)->where('is_published', true)
            ->with(['attempts' => fn ($attempts) => $attempts->where('user_id', $user->id)->latest('id')])->orderBy('id')->get();
        return WorkflowResult::payload(['assessments' => $assessments->map(fn ($assessment) => [
            'id' => (int) $assessment->id, 'course_id' => (int) $assessment->course_id, 'title' => $assessment->title,
            'instructions' => $assessment->instructions, 'pass_mark' => (int) $assessment->pass_mark,
            'attempt_limit' => $assessment->attempt_limit, 'duration_minutes' => $assessment->duration_minutes,
            'attempts_used' => $assessment->attempts->count(), 'attempts_remaining' => $assessment->attempt_limit === null ? null : max(0, $assessment->attempt_limit - $assessment->attempts->count()),
            'latest_attempt' => $assessment->attempts->first()?->only(['id', 'status', 'score', 'passed', 'started_at', 'submitted_at']),
        ])->values()->all()]);
    }

    public function practitionerShow(WorkflowInput $input, CourseAssessment $assessment): WorkflowResult
    {
        $user = $this->practitioner($input);
        $assessment->load('course');
        abort_unless($assessment->is_published && $assessment->course?->is_active, 404);
        $this->assertEnrolled($user, $assessment->course);
        $attempts = CourseAssessmentAttempt::query()->where('assessment_id', $assessment->id)->where('user_id', $user->id)
            ->latest('id')->get(['id', 'status', 'score', 'passed', 'started_at', 'submitted_at']);
        return WorkflowResult::payload(['assessment' => $this->learnerProjection($assessment), 'attempts' => $attempts,
            'attempts_used' => $attempts->count(), 'attempts_remaining' => $assessment->attempt_limit === null ? null : max(0, $assessment->attempt_limit - $attempts->count())]);
    }

    public function attempt(WorkflowInput $input, CourseAssessment $assessment, CourseAssessmentAttempt $attempt): WorkflowResult
    {
        $user = $this->practitioner($input);
        abort_unless((int) $attempt->assessment_id === (int) $assessment->id && (int) $attempt->user_id === (int) $user->id, 404);
        if ($attempt->status === 'in_progress') {
            $deadline = $assessment->duration_minutes ? $attempt->started_at->copy()->addMinutes($assessment->duration_minutes)->toISOString() : null;
            return WorkflowResult::payload(['attempt' => $attempt->only(['id', 'status', 'score', 'passed', 'started_at', 'submitted_at']),
                'answers' => $attempt->answers ?? [], 'deadline' => $deadline]);
        }
        abort_unless($attempt->status === 'submitted', 409, 'Results are available only after submission.');
        $questions = $assessment->questions ?? [];
        $answers = $attempt->answers ?? [];
        $review = array_map(function ($question) use ($answers) {
            $selected = $answers[$question['id']] ?? null;
            $correct = (string) $question['correct_choice_id'];
            $choiceLabels = collect($question['choices'])->keyBy('id');
            return ['question_id' => $question['id'], 'prompt' => $question['prompt'], 'selected_choice_id' => $selected,
                'selected_choice_label' => $choiceLabels->get($selected)['label'] ?? null, 'correct_choice_id' => $correct,
                'correct_choice_label' => $choiceLabels->get($correct)['label'] ?? null,
                'correct' => $selected !== null && hash_equals($correct, (string) $selected), 'explanation' => $question['explanation'] ?? null];
        }, $questions);
        return WorkflowResult::payload(['attempt' => $attempt->only(['id', 'status', 'score', 'passed', 'started_at', 'submitted_at']), 'review' => $review]);
    }
}
