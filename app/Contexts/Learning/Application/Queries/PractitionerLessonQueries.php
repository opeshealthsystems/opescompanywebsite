<?php

namespace App\Contexts\Learning\Application\Queries;

use App\Models\Course;
use App\Models\CourseCertificate;
use App\Models\CourseLesson;
class PractitionerLessonQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\Course $course, \App\Models\CourseLesson $lesson): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($lesson->course_id === $course->id, 404);
        $enrollment = \App\Contexts\Shared\Application\CurrentWorkflow::actor()->courseEnrollments()->where('course_id', $course->id)->firstOrFail();
        $course->load('lessons');
        $completedLessonIds = $enrollment->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id')->toArray();
        $lessons = $course->lessons;
        $currentIndex = $lessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_lessoncontroller_show_1', compact('course', 'lesson', 'enrollment', 'completedLessonIds', 'prevLesson', 'nextLesson'));
    }
}
