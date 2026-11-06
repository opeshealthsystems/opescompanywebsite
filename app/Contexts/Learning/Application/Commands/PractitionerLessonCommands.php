<?php

namespace App\Contexts\Learning\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Course;
use App\Models\CourseCertificate;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseLessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PractitionerLessonCommands extends WorkflowHandler
{
    public function markDone(WorkflowInput $workflowInput, $locale, Course $course, CourseLesson $lesson): WorkflowResult
    {
        abort_unless((int) $lesson->course_id === (int) $course->id, 404);
        $actor = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $outcome = DB::transaction(function () use ($actor, $course, $lesson) {
            $lockedCourse = Course::query()->lockForUpdate()->findOrFail($course->id);
            $lockedLesson = CourseLesson::query()->where('course_id', $lockedCourse->id)->lockForUpdate()->findOrFail($lesson->id);
            $enrollment = CourseEnrollment::query()->where('user_id', $actor->id)->where('course_id', $lockedCourse->id)->lockForUpdate()->firstOrFail();
            abort_unless($enrollment->status !== 'dropped', 403);

            $progress = CourseLessonProgress::query()->where('enrollment_id', $enrollment->id)->where('lesson_id', $lockedLesson->id)->lockForUpdate()->first();
            $newlyCompletedLesson = !$progress || !$progress->completed_at;
            if (!$progress) {
                $progress = CourseLessonProgress::create(['enrollment_id' => $enrollment->id, 'lesson_id' => $lockedLesson->id, 'completed_at' => now()]);
            } elseif (!$progress->completed_at) {
                $progress->update(['completed_at' => now()]);
            }

            $totalLessons = CourseLesson::query()->where('course_id', $lockedCourse->id)->count();
            $doneLessons = CourseLessonProgress::query()->where('enrollment_id', $enrollment->id)->whereNotNull('completed_at')->count();
            if ($enrollment->status === 'enrolled') $enrollment->update(['status' => 'in_progress']);
            $newlyCompletedCourse = $totalLessons > 0 && $doneLessons >= $totalLessons && !$enrollment->isComplete();
            if ($newlyCompletedCourse) $enrollment->update(['status' => 'completed', 'completed_at' => now()]);

            $certificate = null;
            $certificateIssued = false;
            if ($enrollment->isComplete()) {
                $certificate = $enrollment->certificate;
                if (!$certificate) {
                    $certificate = CourseCertificate::create(['enrollment_id' => $enrollment->id, 'user_id' => $enrollment->user_id, 'course_id' => $enrollment->course_id]);
                    $certificateIssued = true;
                }
            }

            if ($newlyCompletedLesson) {
                DomainOutbox::append('event', 'learning.lesson_completed.v1', ['references' => [
                    ['resource' => 'CourseEnrollment', 'id' => $enrollment->id], ['resource' => 'CourseLesson', 'id' => $lockedLesson->id],
                ]], $actor->id);
            }
            if ($newlyCompletedCourse) {
                DomainOutbox::append('event', 'learning.course_completed.v1', ['references' => [
                    ['resource' => 'CourseEnrollment', 'id' => $enrollment->id], ['resource' => 'Course', 'id' => $lockedCourse->id],
                    ['resource' => 'CourseCertificate', 'id' => $certificate->id],
                ]], $actor->id);
            }

            $nextLessonId = null;
            if (!$enrollment->isComplete()) {
                $completedIds = CourseLessonProgress::query()->where('enrollment_id', $enrollment->id)->whereNotNull('completed_at')->pluck('lesson_id')->all();
                $nextLessonId = CourseLesson::query()->where('course_id', $lockedCourse->id)->orderBy('sort_order')->orderBy('id')
                    ->whereNotIn('id', $completedIds)->value('id');
            }

            return [
                'lesson_progress' => ['lesson_id' => $lockedLesson->id, 'completed_at' => $progress->completed_at],
                'enrollment' => ['id' => $enrollment->id, 'course_id' => $enrollment->course_id, 'status' => $enrollment->status,
                    'completed_lessons' => $doneLessons, 'total_lessons' => $totalLessons,
                    'progress_percent' => $totalLessons > 0 ? (int) round($doneLessons / $totalLessons * 100) : 0,
                    'completed_at' => $enrollment->completed_at],
                'next_lesson_id' => $nextLessonId,
                'certificate' => $certificate ? ['id' => $certificate->id, 'certificate_number' => $certificate->certificate_number, 'issued_at' => $certificate->issued_at] : null,
                'certificate_issued' => $certificateIssued,
                'newly_completed_course' => $newlyCompletedCourse,
            ];
        }, 3);

        if ($outcome['certificate_issued']) {
            $certificate = CourseCertificate::findOrFail($outcome['certificate']['id']);
            if (class_exists(\App\Mail\CourseCertificateIssued::class)) {
                \App\Contexts\Shared\Infrastructure\DomainMail::to($actor->email)->queue(new \App\Mail\CourseCertificateIssued($certificate));
            }
            $actor->notify(new \App\Notifications\FeedEntry('learning.certificate', 'Course certificate issued', 'Your certificate for ' . $course->title . ' is ready.', 'academic-cap', route('practitioner.certificates', ['locale' => 'en'])));
        }

        if ($workflowInput->api) {
            $message = $outcome['newly_completed_course'] ? 'Course completed. Your certificate is ready.' : 'Lesson progress saved.';
            unset($outcome['certificate_issued'], $outcome['newly_completed_course']);
            return WorkflowResult::payload($outcome)->with('success', $message);
        }
        if ($outcome['enrollment']['status'] === 'completed') {
            return WorkflowResult::next('practitioner_lessoncontroller_markDone_1', ['locale' => $workflowInput->locale(), 'course' => $course->slug])
                ->with('success', 'Congratulations! You completed the course. Your certificate is ready.');
        }
        if ($outcome['next_lesson_id']) {
            return WorkflowResult::next('practitioner_lessoncontroller_markDone_2', ['locale' => $workflowInput->locale(), 'course' => $course->slug, 'lesson' => $outcome['next_lesson_id']])
                ->with('success', 'Lesson complete!');
        }
        return WorkflowResult::next('practitioner_lessoncontroller_markDone_3', ['locale' => $workflowInput->locale(), 'course' => $course->slug])
            ->with('success', 'Lesson complete!');
    }
}
