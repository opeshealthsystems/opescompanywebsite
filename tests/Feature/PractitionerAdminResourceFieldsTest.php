<?php

namespace Tests\Feature;

use Tests\TestCase;

class PractitionerAdminResourceFieldsTest extends TestCase
{
    public function test_practitioner_admin_resources_expose_assigned_audit_fields(): void
    {
        $profile = file_get_contents(app_path('Filament/Resources/PractitionerProfileResource.php'));
        $application = file_get_contents(app_path('Filament/Resources/PractitionerApplicationResource.php'));
        $bugReport = file_get_contents(app_path('Filament/Resources/PractitionerBugReportResource.php'));

        $this->assertGreaterThanOrEqual(2, substr_count($profile, "make('payout_number')"));
        $this->assertStringContainsString("Section::make('Payout Details')", $application);
        foreach (['payout_reference', 'payout_provider', 'payout_initiated_at', 'paid_at', 'payout_failure_reason'] as $field) {
            $this->assertStringContainsString("make('{$field}')", $application);
        }
        $this->assertStringContainsString("FileUpload::make('screenshot_path')", $bugReport);
    }
}
