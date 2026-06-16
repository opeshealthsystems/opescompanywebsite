<?php

namespace Tests\Feature;

use App\Mail\BugReportResponded;
use App\Models\PractitionerBugReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PractitionerBugReportTest extends TestCase
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

    public function test_practitioner_can_view_bug_reports_index(): void
    {
        $practitioner = $this->practitioner();

        $this->actingAs($practitioner)
            ->get('/en/practitioner/bug-reports')
            ->assertOk()
            ->assertSee('My Bug Reports');
    }

    public function test_practitioner_can_submit_a_bug_report(): void
    {
        $practitioner = $this->practitioner();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/bug-reports', [
                'title'              => 'Login button does nothing',
                'severity'           => 'high',
                'description'        => 'Clicking the login button has no effect on the form.',
                'steps_to_reproduce' => '1. Open login. 2. Click login.',
                'product_slug'       => 'opescare',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_bug_reports', [
            'title'           => 'Login button does nothing',
            'severity'        => 'high',
            'practitioner_id' => $practitioner->id,
            'status'          => 'open',
        ]);
    }

    public function test_validation_rejects_empty_title_and_description(): void
    {
        $practitioner = $this->practitioner();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/bug-reports', [
                'title'       => '',
                'severity'    => 'medium',
                'description' => '',
            ])
            ->assertSessionHasErrors(['title', 'description']);
    }

    public function test_practitioner_cannot_view_another_practitioners_bug_report(): void
    {
        $owner = $this->practitioner();
        $other = $this->practitioner();

        $report = PractitionerBugReport::factory()->create([
            'practitioner_id' => $owner->id,
        ]);

        $this->actingAs($other)
            ->get('/en/practitioner/bug-reports/' . $report->id)
            ->assertForbidden();
    }

    public function test_admin_responding_updates_status_and_queues_mail(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $practitioner = $this->practitioner();

        $report = PractitionerBugReport::factory()->create([
            'practitioner_id' => $practitioner->id,
            'status'          => 'open',
        ]);

        $report->update([
            'status'         => 'resolved',
            'admin_response' => 'Fixed in the latest release.',
            'responded_by'   => $admin->id,
            'responded_at'   => now(),
        ]);

        Mail::to($report->practitioner->email)->queue(new BugReportResponded($report));

        $this->assertDatabaseHas('practitioner_bug_reports', [
            'id'           => $report->id,
            'status'       => 'resolved',
            'responded_by' => $admin->id,
        ]);

        Mail::assertQueued(BugReportResponded::class, function ($mail) use ($report) {
            return $mail->bugReport->id === $report->id;
        });
    }
}
