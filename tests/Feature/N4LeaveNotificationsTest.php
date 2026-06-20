<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;
use App\Notifications\LeaveRequestSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N4LeaveNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function leaveData(User $employee, array $overrides = []): array
    {
        return array_merge([
            'user_id'    => $employee->id,
            'type'       => 'annual',
            'start_date' => '2026-07-10',
            'end_date'   => '2026-07-12',
            'total_days' => 3,
            'status'     => 'pending',
        ], $overrides);
    }

    public function test_submitting_leave_notifies_managers_and_hr(): void
    {
        Notification::fake();
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $hr = User::factory()->create();
        $hr->assignRole('hr');
        $employee = User::factory()->create();

        LeaveRequest::create($this->leaveData($employee));

        Notification::assertSentTo($manager, LeaveRequestSubmitted::class);
        Notification::assertSentTo($hr, LeaveRequestSubmitted::class);
    }

    public function test_approving_leave_notifies_the_employee(): void
    {
        Notification::fake();
        $employee = User::factory()->create();
        $leave    = LeaveRequest::create($this->leaveData($employee));

        $leave->update(['status' => 'approved']);

        Notification::assertSentTo($employee, LeaveApproved::class);
    }

    public function test_rejecting_leave_notifies_the_employee(): void
    {
        Notification::fake();
        $employee = User::factory()->create();
        $leave    = LeaveRequest::create($this->leaveData($employee));

        $leave->update(['status' => 'rejected']);

        Notification::assertSentTo($employee, LeaveRejected::class);
    }
}
