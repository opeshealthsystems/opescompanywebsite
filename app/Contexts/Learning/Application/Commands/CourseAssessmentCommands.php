<?php

namespace App\Contexts\Learning\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Course;
use App\Models\CourseAssessment;
use App\Models\CourseAssessmentAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CourseAssessmentCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function values(WorkflowInput $input, bool $update = false): array
    {
        $values = $input->validate([
            'title' => ($update ? 'sometimes|required|' : 'required|').'string|max:200',
            'instructions' => 'sometimes|nullable|string|max:10000',
            'pass_mark' => 'sometimes|required|integer|min:1|max:100',
            'attempt_limit' => 'sometimes|nullable|integer|min:1|max:100',
            'duration_minutes' => 'sometimes|nullable|integer|min:1|max:1440',
            'questions' => ($update ? 'sometimes|' : 'required|').'array|min:1|max:100',
            'questions.*' => 'required|array:id,prompt,choices,correct_choice_id,points,explanation',
            'questions.*.id' => 'required|string|max:80|distinct',
            'questions.*.prompt' => 'required|string|max:3000',
            'questions.*.choices' => 'required|array|min:2|max:8',
            'questions.*.choices.*' => 'required|array:id,label',
            'questions.*.choices.*.id' => 'required|string|max:40',
            'questions.*.choices.*.label' => 'required|string|max:1000',
            'questions.*.correct_choice_id' => 'required|string|max:40',
            'questions.*.points' => 'sometimes|integer|min:1|max:100',
            'questions.*.explanation' => 'sometimes|nullable|string|max:3000',
            'is_published' => 'sometimes|boolean',
            'course_id' => 'prohibited', 'id' => 'prohibited',
        ]);

        foreach ($values['questions'] ?? [] as $index => &$question) {
            $choiceIds = array_column($question['choices'], 'id');
            if (count($choiceIds) !== count(array_unique($choiceIds))) {
                throw ValidationException::withMessages(["questions.$index.choices" => ['Choice identifiers must be unique within a question.']]);
            }
            if (!in_array($question['correct_choice_id'], $choiceIds, true)) {
                throw ValidationException::withMessages(["questions.$index.correct_choice_id" => ['The correct choice must match one of this question’s choices.']]);
            }
            $question['points'] = (int) ($question['points'] ?? 1);
        }
        unset($question);

        return $values;
    }

    private function assertCourse(Course $course, CourseAssessment $assessment): void
    {
        abort_unless((int) $assessment->course_id === (int) $course->id, 404);
    }

    private function assertPublishable(Course $course, array $values, ?CourseAssessment $assessment = null): void
    {
        if (!($values['is_published'] ?? $assessment?->is_published ?? false)) return;
        $questions = $values['questions'] ?? $assessment?->questions ?? [];
        if (!$course->is_active || count($questions) < 1) {
            throw ValidationException::withMessages(['is_published' => ['Publish an exam only after its course is active and it has at least one valid question.']]);
        }
    }

    public function create(WorkflowInput $input, Course $course): WorkflowResult
    {
        $actor = $this->admin($input);
        $values = $this->values($input);
        $this->assertPublishable($course, $values);
        $assessment = DB::transaction(function () use ($course, $values, $actor) {
            $parent = Course::query()->lockForUpdate()->findOrFail($course->id);
            $record = $parent->assessments()->create($values + ['pass_mark' => 70, 'is_published' => false]);
            DomainOutbox::append('event', 'learning.assessment_created.v1', ['references' => [
                ['resource' => 'Course', 'id' => $parent->id], ['resource' => 'CourseAssessment', 'id' => $record->id],
            ]], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['assessment' => $assessment, 'id' => $assessment->id], 201);
    }

    public function update(WorkflowInput $input, Course $course, CourseAssessment $assessment): WorkflowResult
    {
        $actor = $this->admin($input);
        $this->assertCourse($course, $assessment);
        $values = $this->values($input, true);
        if (!$values) throw ValidationException::withMessages(['assessment' => ['Provide at least one field to update.']]);
        $record = DB::transaction(function () use ($course, $assessment, $values, $actor) {
            Course::query()->lockForUpdate()->findOrFail($course->id);
            $record = CourseAssessment::query()->where('course_id', $course->id)->lockForUpdate()->findOrFail($assessment->id);
            $hasAttempts = $record->attempts()->exists();
            $lockedFieldsChanged = collect(['questions', 'pass_mark', 'attempt_limit', 'duration_minutes'])
                ->contains(fn ($field) => array_key_exists($field, $values) && $values[$field] != $record->{$field});
            if ($hasAttempts && $lockedFieldsChanged) {
                throw ValidationException::withMessages(['assessment' => ['Question content and scoring rules are locked after the first learner attempt. Create a new assessment version for changes.']]);
            }
            $this->assertPublishable($course, $values, $record);
            $record->fill($values);
            if (!$record->isDirty()) return $record;
            $changed = array_keys($record->getDirty());
            $record->save();
            DomainOutbox::append('event', 'learning.assessment_updated.v1', ['references' => [
                ['resource' => 'CourseAssessment', 'id' => $record->id],
            ], 'changed_fields' => $changed], $actor->id);
            if (in_array('is_published', $changed, true)) {
                DomainOutbox::append('event', $record->is_published ? 'learning.assessment_published.v1' : 'learning.assessment_unpublished.v1', [
                    'references' => [['resource' => 'CourseAssessment', 'id' => $record->id]],
                ], $actor->id);
            }
            return $record;
        }, 3);

        return WorkflowResult::payload(['assessment' => $record]);
    }

    public function delete(WorkflowInput $input, Course $course, CourseAssessment $assessment): WorkflowResult
    {
        $actor = $this->admin($input);
        $this->assertCourse($course, $assessment);
        $id = DB::transaction(function () use ($course, $assessment, $actor) {
            Course::query()->lockForUpdate()->findOrFail($course->id);
            $record = CourseAssessment::query()->where('course_id', $course->id)->lockForUpdate()->findOrFail($assessment->id);
            if ($record->attempts()->exists()) throw ValidationException::withMessages(['assessment' => ['Assessments with learner attempts are retained as learning records. Unpublish this assessment instead.']]);
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'learning.assessment_deleted.v1', ['references' => [['resource' => 'CourseAssessment', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    public function startAttempt(WorkflowInput $input, CourseAssessment $assessment): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasRole('practitioner'), 403);
        $attempt = DB::transaction(function () use ($assessment, $actor) {
            $record = CourseAssessment::query()->with('course')->lockForUpdate()->findOrFail($assessment->id);
            abort_unless($record->is_published && $record->course?->is_active, 404);
            $enrollment = $actor->courseEnrollments()->where('course_id', $record->course_id)->lockForUpdate()->first();
            abort_unless($enrollment && $enrollment->status !== 'dropped', 403, 'Enroll in this course before starting its assessment.');

            $current = CourseAssessmentAttempt::query()->where('assessment_id', $record->id)->where('user_id', $actor->id)
                ->where('status', 'in_progress')->lockForUpdate()->latest('id')->first();
            if ($current) {
                $deadline = $record->duration_minutes ? $current->started_at->copy()->addMinutes($record->duration_minutes) : null;
                if (!$deadline || $deadline->isFuture()) return $current;
                $current->update(['status' => 'expired', 'submitted_at' => now()]);
                DomainOutbox::append('event', 'learning.assessment_attempt_expired.v1', ['references' => [['resource' => 'CourseAssessmentAttempt', 'id' => $current->id]]], $actor->id);
            }

            $attempts = CourseAssessmentAttempt::query()->where('assessment_id', $record->id)->where('user_id', $actor->id)->count();
            if ($record->attempt_limit !== null && $attempts >= $record->attempt_limit) {
                throw ValidationException::withMessages(['attempt' => ['The attempt limit for this assessment has been reached.']]);
            }
            $attempt = CourseAssessmentAttempt::query()->create(['assessment_id' => $record->id, 'user_id' => $actor->id, 'status' => 'in_progress', 'started_at' => now()]);
            DomainOutbox::append('event', 'learning.assessment_attempt_started.v1', ['references' => [
                ['resource' => 'CourseAssessment', 'id' => $record->id], ['resource' => 'CourseAssessmentAttempt', 'id' => $attempt->id],
            ]], $actor->id);
            return $attempt;
        }, 3);

        return WorkflowResult::payload(['attempt' => $attempt->only(['id', 'status', 'started_at', 'answers']), 'assessment' => $this->learnerProjection($assessment->fresh()), 'deadline' => $assessment->duration_minutes ? $attempt->started_at->copy()->addMinutes($assessment->duration_minutes)->toISOString() : null], 201);
    }

    public function saveAnswers(WorkflowInput $input, CourseAssessment $assessment, CourseAssessmentAttempt $attempt): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasRole('practitioner'), 403);
        abort_unless((int) $attempt->assessment_id === (int) $assessment->id && (int) $attempt->user_id === (int) $actor->id, 404);
        $data = $input->validate(['answers' => 'required|array|min:1|max:100', 'answers.*' => 'required|string|max:40']);
        $result = DB::transaction(function () use ($assessment, $attempt, $actor, $data) {
            $exam = CourseAssessment::query()->lockForUpdate()->findOrFail($assessment->id);
            $record = CourseAssessmentAttempt::query()->where('assessment_id', $exam->id)->lockForUpdate()->findOrFail($attempt->id);
            abort_unless((int) $record->user_id === (int) $actor->id, 404);
            if ($record->status !== 'in_progress') throw ValidationException::withMessages(['attempt' => ['Answers can be saved only while an attempt is in progress.']]);
            if ($exam->duration_minutes && $record->started_at->copy()->addMinutes($exam->duration_minutes)->isPast()) {
                $record->update(['status' => 'expired', 'submitted_at' => now()]);
                DomainOutbox::append('event', 'learning.assessment_attempt_expired.v1', ['references' => [['resource' => 'CourseAssessmentAttempt', 'id' => $record->id]]], $actor->id);
                return ['expired' => true];
            }
            $questions = collect($exam->questions ?? [])->keyBy(fn ($question) => (string) $question['id']);
            foreach ($data['answers'] as $questionId => $choiceId) {
                $question = $questions->get((string) $questionId);
                if (!$question || !in_array($choiceId, array_column($question['choices'], 'id'), true)) {
                    throw ValidationException::withMessages(['answers' => ['Every saved answer must select a valid choice for a question in this assessment.']]);
                }
            }
            $answers = array_merge($record->answers ?? [], $data['answers']);
            if ($answers !== ($record->answers ?? [])) {
                $record->update(['answers' => $answers]);
                DomainOutbox::append('event', 'learning.assessment_answers_saved.v1', ['references' => [['resource' => 'CourseAssessmentAttempt', 'id' => $record->id]],
                    'answered_count' => count($answers)], $actor->id);
            }
            return ['attempt' => $record->fresh()->only(['id', 'status', 'started_at']), 'answered_count' => count($answers), 'question_count' => $questions->count()];
        }, 3);
        if (($result['expired'] ?? false) === true) throw ValidationException::withMessages(['attempt' => ['The time limit has expired. Start a new attempt if you have attempts remaining.']]);
        return WorkflowResult::payload($result);
    }

    public function submitAttempt(WorkflowInput $input, CourseAssessment $assessment, \App\Models\CourseAssessmentAttempt $attempt): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasRole('practitioner'), 403);
        abort_unless((int) $attempt->assessment_id === (int) $assessment->id && (int) $attempt->user_id === (int) $actor->id, 404);

        $result = DB::transaction(function () use ($assessment, $attempt, $actor) {
            $exam = CourseAssessment::query()->lockForUpdate()->findOrFail($assessment->id);
            $record = CourseAssessmentAttempt::query()->where('assessment_id', $exam->id)->lockForUpdate()->findOrFail($attempt->id);
            abort_unless((int) $record->user_id === (int) $actor->id, 404);
            if ($record->status !== 'in_progress') throw ValidationException::withMessages(['attempt' => ['This attempt has already ended.']]);
            if ($exam->duration_minutes && $record->started_at->copy()->addMinutes($exam->duration_minutes)->isPast()) {
                $record->update(['status' => 'expired', 'submitted_at' => now()]);
                DomainOutbox::append('event', 'learning.assessment_attempt_expired.v1', ['references' => [['resource' => 'CourseAssessmentAttempt', 'id' => $record->id]]], $actor->id);
                return ['expired' => true];
            }

            $questions = $exam->questions ?? [];
            $questionIds = array_map(fn ($question) => (string) $question['id'], $questions);
            $answers = $record->answers ?? [];
            if (count($answers) !== count($questionIds) || array_diff(array_keys($answers), $questionIds) || array_diff($questionIds, array_keys($answers))) {
                throw ValidationException::withMessages(['answers' => ['Submit exactly one selected choice for every question.']]);
            }
            $earned = 0;
            $total = 0;
            $review = [];
            foreach ($questions as $question) {
                $points = (int) ($question['points'] ?? 1);
                $correct = (string) $question['correct_choice_id'];
                $selected = (string) $answers[$question['id']];
                $validChoices = array_column($question['choices'], 'id');
                if (!in_array($selected, $validChoices, true)) throw ValidationException::withMessages(['answers' => ['A selected choice is not part of its question.']]);
                $isCorrect = hash_equals($correct, $selected);
                $total += $points;
                if ($isCorrect) $earned += $points;
                $review[] = ['question_id' => $question['id'], 'selected_choice_id' => $selected, 'correct_choice_id' => $correct, 'correct' => $isCorrect,
                    'explanation' => $question['explanation'] ?? null];
            }
            $score = $total > 0 ? round(($earned / $total) * 100, 2) : 0;
            $passed = $score >= $exam->pass_mark;
            $record->update(['status' => 'submitted', 'score' => $score, 'passed' => $passed, 'answers' => $answers, 'submitted_at' => now()]);
            DomainOutbox::append('event', $passed ? 'learning.assessment_attempt_passed.v1' : 'learning.assessment_attempt_failed.v1', ['references' => [
                ['resource' => 'CourseAssessment', 'id' => $exam->id], ['resource' => 'CourseAssessmentAttempt', 'id' => $record->id],
            ], 'score' => $score], $actor->id);
            return ['attempt' => $record->fresh()->only(['id', 'status', 'score', 'passed', 'started_at', 'submitted_at']), 'review' => $review, 'score' => $score, 'passed' => $passed, 'pass_mark' => $exam->pass_mark];
        }, 3);

        if (($result['expired'] ?? false) === true) {
            throw ValidationException::withMessages(['attempt' => ['The time limit has expired. Start a new attempt if you have attempts remaining.']]);
        }

        return WorkflowResult::payload($result);
    }

    private function learnerProjection(CourseAssessment $assessment): array
    {
        return ['id' => (int) $assessment->id, 'course_id' => (int) $assessment->course_id, 'title' => $assessment->title,
            'instructions' => $assessment->instructions, 'pass_mark' => $assessment->pass_mark, 'attempt_limit' => $assessment->attempt_limit,
            'duration_minutes' => $assessment->duration_minutes, 'questions' => array_map(fn ($question) => [
                'id' => $question['id'], 'prompt' => $question['prompt'], 'choices' => $question['choices'], 'points' => $question['points'] ?? 1,
            ], $assessment->questions ?? [])];
    }
}
