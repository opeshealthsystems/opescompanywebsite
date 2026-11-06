<?php

namespace App\Contexts\Learning\Application\Commands;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use Illuminate\Support\Facades\DB;
class PractitionerCourseCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function enroll(WorkflowInput $workflowInput, $locale, Course $course): WorkflowResult
    {
        abort_unless($course->is_active, 404);
        $actor = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        [$enrollment, $created] = DB::transaction(function () use ($actor, $course) {
            $lockedCourse = Course::query()->lockForUpdate()->findOrFail($course->id);
            abort_unless($lockedCourse->is_active, 404);
            $existing = CourseEnrollment::query()->where('user_id', $actor->id)->where('course_id', $lockedCourse->id)->lockForUpdate()->first();
            if ($existing) return [$existing, false];
            $enrollment = $actor->courseEnrollments()->create(['course_id' => $lockedCourse->id, 'status' => 'enrolled', 'enrolled_at' => now()]);
            DomainOutbox::append('event', 'learning.course_enrolled.v1', ['references' => [
                ['resource' => 'CourseEnrollment', 'id' => $enrollment->id], ['resource' => 'Course', 'id' => $lockedCourse->id],
            ]], $actor->id);
            return [$enrollment, true];
        }, 3);
        if (!$created && $workflowInput->api) {
            return WorkflowResult::payload(['enrollment' => [
                'id' => $enrollment->id, 'course_id' => $enrollment->course_id, 'status' => $enrollment->status,
                'enrolled_at' => $enrollment->enrolled_at, 'completed_at' => $enrollment->completed_at,
            ], 'already_enrolled' => true])->with('success', 'You are already enrolled in this course.');
        }
        if (!$created) {
            return WorkflowResult::next('practitioner_coursecontroller_enroll_1', ['locale' => $workflowInput->locale(), 'course' => $course->slug]);
        }
        if (class_exists(\App\Mail\CourseEnrollmentConfirmed::class)) {
            \App\Contexts\Shared\Infrastructure\DomainMail::to($actor->email)->queue(new \App\Mail\CourseEnrollmentConfirmed($enrollment));
        }
        $actor->notify(new \App\Notifications\FeedEntry('learning.enrolled', 'Enrolled in course', 'You enrolled in ' . $course->title . '.', 'academic-cap', route('practitioner.courses.show', ['locale' => 'en', 'course' => $course->slug])));
        if ($workflowInput->api) {
            return WorkflowResult::payload(['enrollment' => [
                'id' => $enrollment->id, 'course_id' => $enrollment->course_id, 'status' => $enrollment->status,
                'enrolled_at' => $enrollment->enrolled_at, 'completed_at' => $enrollment->completed_at,
            ], 'already_enrolled' => false])->with('success', 'You are now enrolled. Start learning!');
        }
        return WorkflowResult::next('practitioner_coursecontroller_enroll_2', ['locale' => $workflowInput->locale(), 'course' => $course->slug])->with('success', 'You are now enrolled. Start learning!');
    }
}
