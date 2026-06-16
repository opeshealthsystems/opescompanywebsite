<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PublicCourseCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_index_lists_active_course_and_hides_inactive(): void
    {
        $active = Course::factory()->create([
            'title'     => 'Active Health Course',
            'is_active' => true,
        ]);

        $inactive = Course::factory()->create([
            'title'     => 'Hidden Inactive Course',
            'is_active' => false,
        ]);

        $this->get('/en/courses')
            ->assertOk()
            ->assertSee('Active Health Course')
            ->assertDontSee('Hidden Inactive Course');
    }

    public function test_show_active_course_displays_title_and_lessons(): void
    {
        $course = Course::factory()->create([
            'title'     => 'Infection Control Basics',
            'is_active' => true,
        ]);

        CourseLesson::factory()->create([
            'course_id'  => $course->id,
            'title'      => 'Hand Hygiene Protocols',
            'sort_order' => 1,
        ]);
        CourseLesson::factory()->create([
            'course_id'  => $course->id,
            'title'      => 'Sterilization Techniques',
            'sort_order' => 2,
        ]);

        $this->get('/en/courses/' . $course->slug)
            ->assertOk()
            ->assertSee('Infection Control Basics')
            ->assertSee('Hand Hygiene Protocols')
            ->assertSee('Sterilization Techniques');
    }

    public function test_show_inactive_course_returns_404(): void
    {
        $course = Course::factory()->create(['is_active' => false]);

        $this->get('/en/courses/' . $course->slug)
            ->assertNotFound();
    }
}
