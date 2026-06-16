<?php

namespace Tests\Feature;

use App\Mail\CourseCertificateIssued;
use App\Mail\CourseEnrollmentConfirmed;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function practitioner(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        return $user;
    }

    public function test_enroll_creates_enrollment_and_queues_confirmation_mail(): void
    {
        Mail::fake();

        $user = $this->practitioner();
        $course = Course::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->post('/en/practitioner/courses/' . $course->slug . '/enroll')
            ->assertRedirect('/en/practitioner/courses/' . $course->slug);

        $this->assertDatabaseHas('course_enrollments', [
            'course_id' => $course->id,
            'user_id'   => $user->id,
            'status'    => 'enrolled',
        ]);

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)->first();
        $this->assertNotNull($enrollment->enrolled_at);

        Mail::assertQueued(CourseEnrollmentConfirmed::class);
    }

    public function test_duplicate_enroll_does_not_create_second_enrollment(): void
    {
        Mail::fake();

        $user = $this->practitioner();
        $course = Course::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->post('/en/practitioner/courses/' . $course->slug . '/enroll');
        $this->actingAs($user)
            ->post('/en/practitioner/courses/' . $course->slug . '/enroll');

        $this->assertEquals(
            1,
            CourseEnrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)->count()
        );
    }

    public function test_lesson_completion_records_progress_and_flips_to_in_progress(): void
    {
        Mail::fake();

        $user = $this->practitioner();
        $course = Course::factory()->create(['is_active' => true]);
        $lessonA = CourseLesson::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);
        CourseLesson::factory()->create(['course_id' => $course->id, 'sort_order' => 2]);

        $enrollment = CourseEnrollment::factory()->create([
            'course_id' => $course->id,
            'user_id'   => $user->id,
            'status'    => 'enrolled',
        ]);

        $this->actingAs($user)
            ->post('/en/practitioner/courses/' . $course->slug . '/lessons/' . $lessonA->id . '/done')
            ->assertRedirect();

        $this->assertDatabaseHas('course_lesson_progress', [
            'enrollment_id' => $enrollment->id,
            'lesson_id'     => $lessonA->id,
        ]);
        $this->assertNotNull(
            $enrollment->lessonProgress()->where('lesson_id', $lessonA->id)->first()->completed_at
        );

        $this->assertEquals('in_progress', $enrollment->fresh()->status);
    }

    public function test_completing_all_lessons_completes_enrollment_and_issues_certificate(): void
    {
        Mail::fake();

        $user = $this->practitioner();
        $course = Course::factory()->create(['is_active' => true]);
        $lessonA = CourseLesson::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);
        $lessonB = CourseLesson::factory()->create(['course_id' => $course->id, 'sort_order' => 2]);

        $enrollment = CourseEnrollment::factory()->create([
            'course_id' => $course->id,
            'user_id'   => $user->id,
            'status'    => 'enrolled',
        ]);

        $this->actingAs($user)
            ->post('/en/practitioner/courses/' . $course->slug . '/lessons/' . $lessonA->id . '/done');
        $this->actingAs($user)
            ->post('/en/practitioner/courses/' . $course->slug . '/lessons/' . $lessonB->id . '/done');

        $enrollment->refresh();
        $this->assertEquals('completed', $enrollment->status);
        $this->assertNotNull($enrollment->completed_at);

        $this->assertEquals(1, $enrollment->certificate()->count());
        $cert = $enrollment->certificate;
        $this->assertMatchesRegularExpression('/^CERT-\d{4}-\d{6}$/', $cert->certificate_number);

        Mail::assertQueued(CourseCertificateIssued::class);
    }

    public function test_progress_percent_returns_zero_and_hundred(): void
    {
        $user = $this->practitioner();
        $course = Course::factory()->create(['is_active' => true]);
        $lessonA = CourseLesson::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);
        $lessonB = CourseLesson::factory()->create(['course_id' => $course->id, 'sort_order' => 2]);

        $enrollment = CourseEnrollment::factory()->create([
            'course_id' => $course->id,
            'user_id'   => $user->id,
            'status'    => 'enrolled',
        ]);

        $this->assertEquals(0, $enrollment->progressPercent());

        foreach ([$lessonA, $lessonB] as $lesson) {
            $enrollment->lessonProgress()->updateOrCreate(
                ['lesson_id' => $lesson->id],
                ['completed_at' => now()]
            );
        }

        $this->assertEquals(100, $enrollment->fresh()->progressPercent());
    }
}
