<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCertificate;
use App\Models\CourseLesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show($locale, Course $course, CourseLesson $lesson)
    {
        abort_unless($lesson->course_id === $course->id, 404);

        $enrollment = auth()->user()->courseEnrollments()->where('course_id', $course->id)->firstOrFail();
        $course->load('lessons');

        $completedLessonIds = $enrollment->lessonProgress()
            ->whereNotNull('completed_at')->pluck('lesson_id')->toArray();

        $lessons = $course->lessons;
        $currentIndex = $lessons->search(fn ($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        return view('customer.lessons.show', compact(
            'course', 'lesson', 'enrollment', 'completedLessonIds', 'prevLesson', 'nextLesson'
        ));
    }

    public function markDone($locale, Course $course, CourseLesson $lesson)
    {
        abort_unless($lesson->course_id === $course->id, 404);

        $enrollment = auth()->user()->courseEnrollments()->where('course_id', $course->id)->firstOrFail();

        $enrollment->lessonProgress()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        if ($enrollment->status === 'enrolled') {
            $enrollment->update(['status' => 'in_progress']);
        }

        $totalLessons = $course->lessons()->count();
        $doneLessons = $enrollment->lessonProgress()->whereNotNull('completed_at')->count();

        if ($totalLessons > 0 && $doneLessons >= $totalLessons && !$enrollment->isComplete()) {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);

            $cert = $enrollment->certificate;
            if (!$cert) {
                $cert = CourseCertificate::create([
                    'enrollment_id' => $enrollment->id,
                    'user_id'       => $enrollment->user_id,
                    'course_id'     => $enrollment->course_id,
                ]);
            }

            if (class_exists(\App\Mail\CourseCertificateIssued::class)) {
                \Illuminate\Support\Facades\Mail::to(auth()->user()->email)
                    ->queue(new \App\Mail\CourseCertificateIssued($cert));
            }

            return redirect()
                ->route('customer.courses.show', ['locale' => app()->getLocale(), 'course' => $course->slug])
                ->with('success', 'Congratulations! You completed the course. Your certificate is ready.');
        }

        $lessons = $course->lessons;
        $currentIndex = $lessons->search(fn ($l) => $l->id === $lesson->id);
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        if ($nextLesson) {
            return redirect()->route('customer.lessons.show', [
                'locale' => app()->getLocale(), 'course' => $course->slug, 'lesson' => $nextLesson->id,
            ])->with('success', 'Lesson complete!');
        }

        return redirect()
            ->route('customer.courses.show', ['locale' => app()->getLocale(), 'course' => $course->slug])
            ->with('success', 'Lesson complete!');
    }
}
