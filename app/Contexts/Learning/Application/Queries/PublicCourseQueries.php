<?php

namespace App\Contexts\Learning\Application\Queries;

use App\Models\Course;
class PublicCourseQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $courses = $workflowInput->collect(\App\Models\Course::where('is_active', true)->withCount('enrollments')->orderBy('sort_order'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('publiccoursecontroller_index_1', compact('courses'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\Course $course): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($course->is_active, 404);
        $course->load('lessons');
        $enrollment = null;
        if (\App\Contexts\Shared\Application\CurrentWorkflow::actor()) {
            $enrollment = $course->enrollments()->where('user_id', \App\Contexts\Shared\Application\CurrentWorkflow::id())->first();
        }
        return \App\Contexts\Shared\Application\WorkflowResult::read('publiccoursecontroller_show_1', compact('course', 'enrollment'));
    }
}
