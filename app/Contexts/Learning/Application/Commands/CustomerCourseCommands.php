<?php

namespace App\Contexts\Learning\Application\Commands;

use App\Models\Course;
class CustomerCourseCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function enroll(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\Course $course): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($course->is_active, 404);
        $existing = \App\Contexts\Shared\Application\CurrentWorkflow::actor()->courseEnrollments()->where('course_id', $course->id)->first();
        if ($existing) {
            return \App\Contexts\Shared\Application\WorkflowResult::next('customer_coursecontroller_enroll_1', ['locale' => $workflowInput->locale(), 'course' => $course->slug]);
        }
        $enrollment = \App\Contexts\Shared\Application\CurrentWorkflow::actor()->courseEnrollments()->create(['course_id' => $course->id, 'status' => 'enrolled', 'enrolled_at' => now()]);
        if (class_exists(\App\Mail\CourseEnrollmentConfirmed::class)) {
            \App\Contexts\Shared\Infrastructure\DomainMail::to(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->email)->queue(new \App\Mail\CourseEnrollmentConfirmed($enrollment));
        }
        \App\Contexts\Shared\Application\CurrentWorkflow::actor()->notify(new \App\Notifications\FeedEntry('learning.enrolled', 'Enrolled in course', 'You enrolled in ' . $course->title . '.', 'academic-cap', route('customer.courses.show', ['locale' => 'en', 'course' => $course->slug])));
        return \App\Contexts\Shared\Application\WorkflowResult::next('customer_coursecontroller_enroll_2', ['locale' => $workflowInput->locale(), 'course' => $course->slug])->with('success', 'You are now enrolled. Start learning!');
    }
}
