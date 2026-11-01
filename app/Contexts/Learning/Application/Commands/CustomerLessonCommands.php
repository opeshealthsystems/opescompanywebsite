<?php

namespace App\Contexts\Learning\Application\Commands;

use App\Models\Course;
use App\Models\CourseCertificate;
use App\Models\CourseLesson;
class CustomerLessonCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function markDone(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\Course $course, \App\Models\CourseLesson $lesson): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($lesson->course_id === $course->id, 404);
        $enrollment = \App\Contexts\Shared\Application\CurrentWorkflow::actor()->courseEnrollments()->where('course_id', $course->id)->firstOrFail();
        $enrollment->lessonProgress()->updateOrCreate(['lesson_id' => $lesson->id], ['completed_at' => now()]);
        if ($enrollment->status === 'enrolled') {
            $enrollment->update(['status' => 'in_progress']);
        }
        $totalLessons = $course->lessons()->count();
        $doneLessons = $enrollment->lessonProgress()->whereNotNull('completed_at')->count();
        if ($totalLessons > 0 && $doneLessons >= $totalLessons && !$enrollment->isComplete()) {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);
            $cert = $enrollment->certificate;
            if (!$cert) {
                $cert = \App\Models\CourseCertificate::create(['enrollment_id' => $enrollment->id, 'user_id' => $enrollment->user_id, 'course_id' => $enrollment->course_id]);
            }
            if (class_exists(\App\Mail\CourseCertificateIssued::class)) {
                \App\Contexts\Shared\Infrastructure\DomainMail::to(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->email)->queue(new \App\Mail\CourseCertificateIssued($cert));
            }
            \App\Contexts\Shared\Application\CurrentWorkflow::actor()->notify(new \App\Notifications\FeedEntry('learning.certificate', 'Course certificate issued', 'Your certificate for ' . $course->title . ' is ready.', 'academic-cap', route('customer.certificates', ['locale' => 'en'])));
            return \App\Contexts\Shared\Application\WorkflowResult::next('customer_lessoncontroller_markDone_1', ['locale' => $workflowInput->locale(), 'course' => $course->slug])->with('success', 'Congratulations! You completed the course. Your certificate is ready.');
        }
        $lessons = $course->lessons;
        $currentIndex = $lessons->search(fn($l) => $l->id === $lesson->id);
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;
        if ($nextLesson) {
            return \App\Contexts\Shared\Application\WorkflowResult::next('customer_lessoncontroller_markDone_2', ['locale' => $workflowInput->locale(), 'course' => $course->slug, 'lesson' => $nextLesson->id])->with('success', 'Lesson complete!');
        }
        return \App\Contexts\Shared\Application\WorkflowResult::next('customer_lessoncontroller_markDone_3', ['locale' => $workflowInput->locale(), 'course' => $course->slug])->with('success', 'Lesson complete!');
    }
}
