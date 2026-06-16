<?php

namespace App\Http\Controllers;

use App\Models\Course;

class PublicCourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_active', true)
            ->withCount('enrollments')
            ->orderBy('sort_order')
            ->get();

        return view('pages.courses.index', compact('courses'));
    }

    public function show($locale, Course $course)
    {
        abort_unless($course->is_active, 404);
        $course->load('lessons');

        $enrollment = null;
        if (auth()->check()) {
            $enrollment = $course->enrollments()->where('user_id', auth()->id())->first();
        }

        return view('pages.courses.show', compact('course', 'enrollment'));
    }
}
