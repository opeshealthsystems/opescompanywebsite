<?php

namespace Tests\Feature;

use App\Models\TesterAssignment;
use App\Models\User;
use App\Notifications\NewTesterAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N3AssignmentNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_creating_an_assignment_notifies_the_tester(): void
    {
        Notification::fake();
        $tester = User::factory()->create();
        $tester->assignRole('tester');
        $admin  = User::factory()->create();

        TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'ohos',
            'product_name' => 'OPES Health OS',
            'title'        => 'Test the triage workflow',
            'description'  => 'Validate the triage workflow end to end.',
            'status'       => 'pending',
        ]);

        Notification::assertSentTo($tester, NewTesterAssignment::class);
    }
}
