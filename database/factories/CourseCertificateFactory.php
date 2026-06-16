<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseCertificate;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseCertificate>
 */
class CourseCertificateFactory extends Factory
{
    protected $model = CourseCertificate::class;

    public function definition(): array
    {
        return [
            'enrollment_id' => CourseEnrollment::factory(),
            'user_id'       => User::factory(),
            'course_id'     => Course::factory(),
        ];
    }
}
