<?php

namespace Database\Factories;

use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseLessonProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseLessonProgress>
 */
class CourseLessonProgressFactory extends Factory
{
    protected $model = CourseLessonProgress::class;

    public function definition(): array
    {
        return [
            'enrollment_id' => CourseEnrollment::factory(),
            'lesson_id'     => CourseLesson::factory(),
            'completed_at'  => now(),
        ];
    }
}
