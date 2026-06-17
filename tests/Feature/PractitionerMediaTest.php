<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PractitionerMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function verifiedPractitioner(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create([
            'profession'        => 'doctor',
            'workplace_country' => 'CM',
            'is_verified'       => true,
        ]);
        $user->refresh();
        return $user;
    }

    // ── Feature 1: Screenshot uploads ─────────────────────────────────────

    public function test_finding_submission_stores_uploaded_screenshot(): void
    {
        Storage::fake('public');

        $practitioner = $this->verifiedPractitioner();
        $application  = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->post('/en/practitioner/applications/' . $application->id . '/findings', [
                'overall_rating' => 5,
                'findings_text'  => 'Looks great.',
                'screenshot'     => File::image('shot.jpg'),
            ])
            ->assertRedirect();

        $finding = PractitionerFinding::firstOrFail();
        $this->assertNotNull($finding->screenshot_path);
        Storage::disk('public')->assertExists($finding->screenshot_path);
        $this->assertStringStartsWith('finding-screenshots/', $finding->screenshot_path);
    }

    public function test_bug_report_submission_stores_uploaded_screenshot(): void
    {
        Storage::fake('public');

        $practitioner = $this->verifiedPractitioner();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/bug-reports', [
                'title'       => 'Button does nothing',
                'severity'    => 'high',
                'description' => 'Clicking submit has no effect at all.',
                'screenshot'  => File::image('bug.png'),
            ])
            ->assertRedirect();

        $report = $practitioner->practitionerBugReports()->firstOrFail();
        $this->assertNotNull($report->screenshot_path);
        Storage::disk('public')->assertExists($report->screenshot_path);
        $this->assertStringStartsWith('bug-report-screenshots/', $report->screenshot_path);
    }
}
