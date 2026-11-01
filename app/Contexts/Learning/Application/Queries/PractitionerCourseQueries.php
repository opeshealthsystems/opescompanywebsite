<?php

namespace App\Contexts\Learning\Application\Queries;

use App\Models\Course;
class PractitionerCourseQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $courses = $workflowInput->collect(\App\Models\Course::where('is_active', true)->withCount('lessons')->orderBy('sort_order'));

        // The native client needs a compact progress summary for its course cards.
        // Keep the existing Eloquent collections intact for the web dashboard.
        if ($workflowInput->api) {
            $courseIds = $courses->pluck('id');
            $myEnrollments = \App\Contexts\Shared\Application\CurrentWorkflow::actor()
                ->courseEnrollments()
                ->whereIn('course_id', $courseIds)
                ->with(['lessonProgress', 'certificate'])
                ->get()
                ->map(function ($enrollment) use ($courses) {
                    $course = $courses->firstWhere('id', $enrollment->course_id);
                    $totalLessons = (int) ($course?->lessons_count ?? 0);
                    $completedLessonIds = $enrollment->lessonProgress
                        ->whereNotNull('completed_at')
                        ->pluck('lesson_id')
                        ->values()
                        ->all();

                    return [
                        'id' => $enrollment->id,
                        'course_id' => $enrollment->course_id,
                        'status' => $enrollment->status,
                        'progress_percent' => $totalLessons > 0 ? (int) round(count($completedLessonIds) / $totalLessons * 100) : 0,
                        'completed_lesson_ids' => $completedLessonIds,
                        'total_lessons' => $totalLessons,
                        'certificate_id' => $enrollment->certificate?->id,
                        'enrolled_at' => $enrollment->enrolled_at,
                        'completed_at' => $enrollment->completed_at,
                    ];
                })
                ->values();

            return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_coursecontroller_index_1', compact('courses', 'myEnrollments'));
        }

        $myEnrollments = $workflowInput->collect(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->courseEnrollments()->with('course'))->keyBy('course_id');
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_coursecontroller_index_1', compact('courses', 'myEnrollments'));
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
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_coursecontroller_show_1', compact('course', 'enrollment', 'completedLessonIds'));
    }
}
