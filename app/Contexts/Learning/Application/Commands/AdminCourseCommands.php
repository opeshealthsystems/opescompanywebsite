<?php

namespace App\Contexts\Learning\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminCourseCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function courseValues(WorkflowInput $input, bool $update = false): array
    {
        $values = $input->validate([
            'title' => ($update ? 'sometimes|required|' : 'required|').'string|max:200',
            'title_fr' => 'sometimes|nullable|string|max:200',
            'slug' => [...($update ? ['sometimes', 'required'] : ['required']), 'string', 'max:100', Rule::unique('courses', 'slug')->ignore($input->route('course') instanceof Course ? $input->route('course')->id : $input->route('course'))],
            'description' => 'sometimes|nullable|string|max:20000', 'description_fr' => 'sometimes|nullable|string|max:20000',
            'level' => [...($update ? ['sometimes', 'required'] : ['required']), Rule::in(array_keys(Course::levelOptions()))],
            'duration_hours' => 'sometimes|nullable|integer|min:1|max:65535',
            'product_slug' => 'sometimes|nullable|string|max:100',
            'cover_image' => 'sometimes|nullable|string|max:300',
            'cover_image_file' => 'sometimes|image|max:2048',
            'is_active' => 'sometimes|boolean', 'is_featured' => 'sometimes|boolean', 'sort_order' => 'sometimes|integer|min:0|max:65535',
            'id' => 'prohibited',
        ]);
        if ($input->hasFile('cover_image_file')) {
            $values['cover_image'] = $input->file('cover_image_file')->storePublicly('courses', 'public');
            unset($values['cover_image_file']);
        } elseif (isset($values['cover_image'])) {
            $path = $values['cover_image'];
            $validPath = str_starts_with($path, 'courses/') && !str_contains($path, '..') && !str_contains($path, '\\')
                && Storage::disk('public')->exists($path);
            if ($path !== '' && $path !== null && (!$validPath || !str_starts_with((string) Storage::disk('public')->mimeType($path), 'image/'))) {
                throw ValidationException::withMessages(['cover_image' => 'Select an existing course image stored in the courses directory.']);
            }
        }
        return $values;
    }

    public function createCourse(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->courseValues($input);
        $record = DB::transaction(function () use ($data, $actor) {
            $record = Course::create($data + ['is_active' => true, 'is_featured' => false, 'sort_order' => 0]);
            DomainOutbox::append('event', 'learning.course_created.v1', ['references' => [['resource' => 'Course', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateCourse(WorkflowInput $input, Course $course): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->courseValues($input, true);
        if (!$data) throw ValidationException::withMessages(['course' => 'Provide at least one course field to update.']);
        $record = DB::transaction(function () use ($course, $data, $actor) {
            $record = Course::query()->lockForUpdate()->findOrFail($course->id);
            $record->update($data);
            DomainOutbox::append('event', 'learning.course_updated.v1', ['references' => [['resource' => 'Course', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteCourse(WorkflowInput $input, Course $course): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($course, $actor) {
            $record = Course::query()->lockForUpdate()->findOrFail($course->id);
            if ($record->enrollments()->exists() || $record->certificates()->exists()) throw ValidationException::withMessages(['course' => 'Courses with enrollment or certificate history cannot be deleted. Deactivate the course instead.']);
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'learning.course_deleted.v1', ['references' => [['resource' => 'Course', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    private function lessonValues(WorkflowInput $input, bool $update = false): array
    {
        return $input->validate([
            'title' => ($update ? 'sometimes|required|' : 'required|').'string|max:200',
            'title_fr' => 'sometimes|nullable|string|max:200', 'content' => 'sometimes|nullable|string|max:100000',
            'content_fr' => 'sometimes|nullable|string|max:100000', 'video_url' => 'sometimes|nullable|url|max:500',
            'duration_minutes' => 'sometimes|nullable|integer|min:1|max:65535', 'sort_order' => 'sometimes|integer|min:0|max:65535',
            'course_id' => 'prohibited', 'id' => 'prohibited',
        ]);
    }

    private function assertLessonCourse(Course $course, CourseLesson $lesson): void
    {
        if ((int) $lesson->course_id !== (int) $course->id) abort(404);
    }

    public function createLesson(WorkflowInput $input, Course $course): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->lessonValues($input);
        $record = DB::transaction(function () use ($course, $data, $actor) {
            $parent = Course::query()->lockForUpdate()->findOrFail($course->id);
            $record = $parent->lessons()->create($data + ['sort_order' => 0]);
            DomainOutbox::append('event', 'learning.course_lesson_created.v1', ['references' => [['resource' => 'Course', 'id' => $parent->id], ['resource' => 'CourseLesson', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateLesson(WorkflowInput $input, Course $course, CourseLesson $lesson): WorkflowResult
    {
        $actor = $this->admin($input); $this->assertLessonCourse($course, $lesson); $data = $this->lessonValues($input, true);
        if (!$data) throw ValidationException::withMessages(['lesson' => 'Provide at least one lesson field to update.']);
        $record = DB::transaction(function () use ($course, $lesson, $data, $actor) {
            $parent = Course::query()->lockForUpdate()->findOrFail($course->id);
            $record = CourseLesson::query()->where('course_id', $parent->id)->lockForUpdate()->findOrFail($lesson->id);
            $record->update($data);
            DomainOutbox::append('event', 'learning.course_lesson_updated.v1', ['references' => [['resource' => 'Course', 'id' => $parent->id], ['resource' => 'CourseLesson', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteLesson(WorkflowInput $input, Course $course, CourseLesson $lesson): WorkflowResult
    {
        $actor = $this->admin($input); $this->assertLessonCourse($course, $lesson);
        $id = DB::transaction(function () use ($course, $lesson, $actor) {
            $parent = Course::query()->lockForUpdate()->findOrFail($course->id);
            $record = CourseLesson::query()->where('course_id', $parent->id)->lockForUpdate()->findOrFail($lesson->id);
            if ($record->progress()->exists()) throw ValidationException::withMessages(['lesson' => 'Lessons with learner progress must be retained.']);
            $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'learning.course_lesson_deleted.v1', ['references' => [['resource' => 'Course', 'id' => $parent->id], ['resource' => 'CourseLesson', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
