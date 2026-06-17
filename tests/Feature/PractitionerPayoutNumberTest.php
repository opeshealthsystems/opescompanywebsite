<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PractitionerPayoutNumberTest extends TestCase
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

    public function test_practitioner_can_save_payout_number(): void
    {
        $user = $this->practitioner();

        $this->actingAs($user)
            ->put('/en/practitioner/profile', [
                'name'          => $user->name,
                'profession'    => 'doctor',
                'payout_number' => '+237 677 123 456',
            ])
            ->assertRedirect();

        // normalise() stores the local 9-digit MSISDN (237 country code stripped);
        // the MoMo driver re-prepends 237 for the API partyId.
        $this->assertSame('677123456', $user->practitionerProfile->fresh()->payout_number);
    }

    public function test_payout_number_rejects_non_numeric(): void
    {
        $user = $this->practitioner();

        $this->actingAs($user)
            ->put('/en/practitioner/profile', [
                'name'          => $user->name,
                'profession'    => 'doctor',
                'payout_number' => 'not-a-number',
            ])
            ->assertSessionHasErrors('payout_number');
    }
}
