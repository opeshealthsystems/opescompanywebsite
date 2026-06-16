<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseLesson>
 */
class CourseLessonFactory extends Factory
{
    protected $model = CourseLesson::class;

    public function definition(): array
    {
        return [
            'course_id'        => Course::factory(),
            'title'            => fake()->sentence(4),
            'content'          => fake()->paragraphs(3, true),
            'video_url'        => fake()->url(),
            'duration_minutes' => fake()->numberBetween(5, 90),
            'sort_order'       => 0,
        ];
    }
}
