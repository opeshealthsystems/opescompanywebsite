<?php

namespace App\Contexts\Learning\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\CourseCertificate;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminLearningRecordQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function enrollmentQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return CourseEnrollment::query()
            ->with(['user:id,name', 'course' => fn (Builder $query) => $query->withCount('lessons')])
            ->withCount(['lessonProgress as completed_lessons_count' => fn (Builder $query) => $query->whereNotNull('completed_at')])
            ->orderByDesc('enrolled_at');
    }

    public function enrollments(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:enrolled,in_progress,completed,dropped',
            'course_id' => 'sometimes|integer|exists:courses,id',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->enrollmentQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['course_id'])) $query->where('course_id', $filters['course_id']);
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn (Builder $builder) => $builder
                ->whereHas('user', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%'))
                ->orWhereHas('course', fn (Builder $course) => $course->where('title', 'like', '%'.$search.'%')));
        }
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (CourseEnrollment $enrollment) => [
                'id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'user_name' => $enrollment->user?->name,
                'course_id' => $enrollment->course_id,
                'course_title' => $enrollment->course?->title,
                'status' => $enrollment->status,
                'progress_percent' => $enrollment->course?->lessons_count
                    ? (int) round($enrollment->completed_lessons_count / $enrollment->course->lessons_count * 100)
                    : 0,
                'enrolled_at' => $enrollment->enrolled_at?->toIso8601String(),
                'completed_at' => $enrollment->completed_at?->toIso8601String(),
            ])->values()->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }

    public function certificateQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return CourseCertificate::query()->with(['user:id,name', 'course:id,title'])->orderByDesc('issued_at');
    }

    public function certificates(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'course_id' => 'sometimes|integer|exists:courses,id',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->certificateQuery($input);
        if (isset($filters['course_id'])) $query->where('course_id', $filters['course_id']);
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn (Builder $builder) => $builder
                ->where('certificate_number', 'like', '%'.$search.'%')
                ->orWhereHas('user', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%'))
                ->orWhereHas('course', fn (Builder $course) => $course->where('title', 'like', '%'.$search.'%')));
        }
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (CourseCertificate $certificate) => [
                'id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'enrollment_id' => $certificate->enrollment_id,
                'user_id' => $certificate->user_id,
                'user_name' => $certificate->user?->name,
                'course_id' => $certificate->course_id,
                'course_title' => $certificate->course?->title,
                'issued_at' => $certificate->issued_at?->toIso8601String(),
            ])->values()->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }
}
