<?php

namespace App\Contexts\Learning\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Database\Eloquent\Builder;

final class AdminCourseQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function courseItem(Course $course): array
    {
        return $course->only(['id', 'product_slug', 'title', 'title_fr', 'slug', 'description', 'description_fr', 'level',
            'duration_hours', 'cover_image', 'is_active', 'is_featured', 'sort_order', 'created_at', 'updated_at']);
    }

    private function lessonItem(CourseLesson $lesson): array
    {
        return $lesson->only(['id', 'course_id', 'title', 'title_fr', 'content', 'content_fr', 'video_url', 'duration_minutes',
            'sort_order', 'created_at', 'updated_at']);
    }

    public function courseQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return Course::query()->withCount('enrollments')->orderBy('sort_order')->orderBy('id');
    }

    public function courses(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['level' => 'sometimes|in:'.implode(',', array_keys(Course::levelOptions())),
            'is_active' => 'sometimes|boolean', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->courseQuery($input);
        if (isset($filters['level'])) $query->where('level', $filters['level']);
        if (array_key_exists('is_active', $filters)) $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        if (!empty($filters['search'])) { $search = trim($filters['search']); $query->where(fn ($q) => $q->where('title', 'like', '%'.$search.'%')->orWhere('slug', 'like', '%'.$search.'%')); }
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (Course $course) => $this->courseItem($course) + ['enrollments_count' => $course->enrollments_count])->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function course(WorkflowInput $input, Course $course): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['course' => $this->courseItem($course)]);
    }

    public function lessons(WorkflowInput $input, Course $course): WorkflowResult
    {
        $this->admin($input);
        $page = $input->paginate($this->lessonQuery($input, $course));
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (CourseLesson $lesson) => $this->lessonItem($lesson))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function lessonQuery(WorkflowInput $input, Course $course): Builder
    {
        $this->admin($input);

        return $course->lessons()->getQuery()->orderBy('sort_order')->orderBy('id');
    }

    public function lesson(WorkflowInput $input, Course $course, CourseLesson $lesson): WorkflowResult
    {
        $this->admin($input);
        if ((int) $lesson->course_id !== (int) $course->id) abort(404);
        return WorkflowResult::payload(['lesson' => $this->lessonItem($lesson)]);
    }
}
