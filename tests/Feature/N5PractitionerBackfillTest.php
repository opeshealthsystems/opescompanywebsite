<?php

namespace Tests\Feature;

use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N5PractitionerBackfillTest extends TestCase
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
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);

        return $user;
    }

    public function test_applying_to_a_program_adds_a_feed_row(): void
    {
        Mail::fake();
        $practitioner = $this->practitioner();
        $program      = PractitionerProgram::factory()->create();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/programs/' . $program->id . '/apply', [
                'motivation' => 'I would love to contribute to this programme.',
            ])
            ->assertRedirect();

        $this->assertContains(
            'practitioner.application_received',
            $practitioner->fresh()->notifications->pluck('data.type')->all(),
        );
    }
}
