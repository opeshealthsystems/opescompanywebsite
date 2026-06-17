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

    // ── Feature 2: Video embed ────────────────────────────────────────────

    public function test_embed_url_converts_youtube_watch_url(): void
    {
        $finding = new PractitionerFinding(['video_url' => 'https://www.youtube.com/watch?v=ABC123']);
        $this->assertSame('https://www.youtube.com/embed/ABC123', $finding->embedUrl());
    }

    public function test_embed_url_converts_youtube_short_url(): void
    {
        $finding = new PractitionerFinding(['video_url' => 'https://youtu.be/ABC123']);
        $this->assertSame('https://www.youtube.com/embed/ABC123', $finding->embedUrl());
    }

    public function test_embed_url_converts_vimeo_url(): void
    {
        $finding = new PractitionerFinding(['video_url' => 'https://vimeo.com/123456']);
        $this->assertSame('https://player.vimeo.com/video/123456', $finding->embedUrl());
    }

    public function test_embed_url_returns_null_for_non_video_url(): void
    {
        $finding = new PractitionerFinding(['video_url' => 'https://example.com/some-page']);
        $this->assertNull($finding->embedUrl());

        $empty = new PractitionerFinding(['video_url' => null]);
        $this->assertNull($empty->embedUrl());
    }

    public function test_store_rejects_non_youtube_vimeo_video_url(): void
    {
        $practitioner = $this->verifiedPractitioner();
        $application  = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->post('/en/practitioner/applications/' . $application->id . '/findings', [
                'overall_rating' => 4,
                'video_url'      => 'https://example.com/not-a-video',
            ])
            ->assertSessionHasErrors('video_url');

        $this->assertDatabaseCount('practitioner_findings', 0);
    }

    public function test_store_accepts_youtube_video_url(): void
    {
        $practitioner = $this->verifiedPractitioner();
        $application  = PractitionerApplication::factory()->approved()->create([
            'practitioner_id' => $practitioner->id,
        ]);

        $this->actingAs($practitioner)
            ->post('/en/practitioner/applications/' . $application->id . '/findings', [
                'overall_rating' => 4,
                'video_url'      => 'https://www.youtube.com/watch?v=ABC123',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_findings', [
            'application_id' => $application->id,
            'video_url'      => 'https://www.youtube.com/watch?v=ABC123',
        ]);
    }
}
