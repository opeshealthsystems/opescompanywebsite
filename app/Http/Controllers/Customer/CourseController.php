<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_active', true)->orderBy('sort_order')->get();
        $myEnrollments = auth()->user()->courseEnrollments()->with('course')->get()->keyBy('course_id');

        return view('customer.courses.index', compact('courses', 'myEnrollments'));
    }

    public function show($locale, Course $course)
    {
        abort_unless($course->is_active, 404);
        $course->load('lessons');

        $enrollment = auth()->user()->courseEnrollments()->where('course_id', $course->id)->first();
        $completedLessonIds = [];
        if ($enrollment) {
            $completedLessonIds = $enrollment->lessonProgress()
                ->whereNotNull('completed_at')->pluck('lesson_id')->toArray();
        }

        return view('customer.courses.show', compact('course', 'enrollment', 'completedLessonIds'));
    }

    public function enroll($locale, Course $course)
    {
        abort_unless($course->is_active, 404);

        $existing = auth()->user()->courseEnrollments()->where('course_id', $course->id)->first();
        if ($existing) {
            return redirect()->route('customer.courses.show', ['locale' => app()->getLocale(), 'course' => $course->slug]);
        }

        $enrollment = auth()->user()->courseEnrollments()->create([
            'course_id'   => $course->id,
            'status'      => 'enrolled',
            'enrolled_at' => now(),
        ]);

        if (class_exists(\App\Mail\CourseEnrollmentConfirmed::class)) {
            \Illuminate\Support\Facades\Mail::to(auth()->user()->email)
                ->queue(new \App\Mail\CourseEnrollmentConfirmed($enrollment));
        }

        return redirect()
            ->route('customer.courses.show', ['locale' => app()->getLocale(), 'course' => $course->slug])
            ->with('success', 'You are now enrolled. Start learning!');
    }
}
