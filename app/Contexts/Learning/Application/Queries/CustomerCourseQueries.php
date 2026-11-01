<?php

namespace App\Contexts\Learning\Application\Queries;

use App\Models\Course;
class CustomerCourseQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $courses = $workflowInput->collect(\App\Models\Course::where('is_active', true)->orderBy('sort_order'));
        $myEnrollments = $workflowInput->collect(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->courseEnrollments()->with('course'))->keyBy('course_id');
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_coursecontroller_index_1', compact('courses', 'myEnrollments'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\Course $course): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($course->is_active, 404);
        $course->load('lessons');
        $enrollment = \App\Contexts\Shared\Application\CurrentWorkflow::actor()->courseEnrollments()->where('course_id', $course->id)->first();
        $completedLessonIds = [];
        if ($enrollment) {
            $completedLessonIds = $enrollment->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id')->toArray();
        }
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_coursecontroller_show_1', compact('course', 'enrollment', 'completedLessonIds'));
    }
}
