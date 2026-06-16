<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\PractitionerProfile;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PractitionerTestimonialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_verified_practitioner_testimonial_appears(): void
    {
        $user = User::factory()->create(['name' => 'Dr Verified Visible']);
        PractitionerProfile::create([
            'user_id' => $user->id,
            'profession' => 'doctor',
            'workplace_name' => 'Yaounde General Hospital',
            'opes_testimonial' => 'OPES transformed how our clinic handles patient records.',
            'is_verified' => true,
        ]);

        $this->get('/en/practitioners')
            ->assertOk()
            ->assertSee('Dr Verified Visible')
            ->assertSee('OPES transformed how our clinic handles patient records.')
            ->assertSee('Yaounde General Hospital');
    }

    public function test_unverified_practitioner_testimonial_is_hidden(): void
    {
        $user = User::factory()->create(['name' => 'Dr Unverified Hidden']);
        PractitionerProfile::create([
            'user_id' => $user->id,
            'profession' => 'nurse',
            'opes_testimonial' => 'This unverified testimonial should never appear.',
            'is_verified' => false,
        ]);

        $this->get('/en/practitioners')
            ->assertOk()
            ->assertDontSee('This unverified testimonial should never appear.');
    }

    public function test_verified_practitioner_with_empty_testimonial_is_hidden(): void
    {
        $nullUser = User::factory()->create(['name' => 'Dr Null Testimonial']);
        PractitionerProfile::create([
            'user_id' => $nullUser->id,
            'profession' => 'doctor',
            'opes_testimonial' => null,
            'is_verified' => true,
        ]);

        $emptyUser = User::factory()->create(['name' => 'Dr Empty Testimonial']);
        PractitionerProfile::create([
            'user_id' => $emptyUser->id,
            'profession' => 'doctor',
            'opes_testimonial' => '',
            'is_verified' => true,
        ]);

        $this->get('/en/practitioners')
            ->assertOk()
            ->assertDontSee('Dr Null Testimonial')
            ->assertDontSee('Dr Empty Testimonial');
    }

    public function test_published_finding_appears_unpublished_does_not(): void
    {
        $program = PractitionerProgram::create([
            'title' => 'OPES EMR Testing Programme',
            'status' => 'open',
        ]);

        $publishedUser = User::factory()->create(['name' => 'Dr Published Finder']);
        $publishedApp = PractitionerApplication::create([
            'practitioner_id' => $publishedUser->id,
            'program_id' => $program->id,
            'status' => 'approved',
        ]);
        PractitionerFinding::create([
            'application_id' => $publishedApp->id,
            'practitioner_id' => $publishedUser->id,
            'overall_rating' => 5,
            'findings_text' => 'The triage workflow is intuitive and fast to navigate.',
            'is_published' => true,
        ]);

        $unpublishedUser = User::factory()->create(['name' => 'Dr Draft Finder']);
        $unpublishedApp = PractitionerApplication::create([
            'practitioner_id' => $unpublishedUser->id,
            'program_id' => $program->id,
            'status' => 'approved',
        ]);
        PractitionerFinding::create([
            'application_id' => $unpublishedApp->id,
            'practitioner_id' => $unpublishedUser->id,
            'overall_rating' => 3,
            'findings_text' => 'This draft finding should remain private.',
            'is_published' => false,
        ]);

        $this->get('/en/practitioners')
            ->assertOk()
            ->assertSee('The triage workflow is intuitive and fast to navigate.')
            ->assertDontSee('This draft finding should remain private.');
    }
}
