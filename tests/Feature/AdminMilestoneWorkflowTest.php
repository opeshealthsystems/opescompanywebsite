<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminMilestoneWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_nested_milestone_crud_uses_project_commands_and_retains_completed_history(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $project = Project::create([
            'reference' => Project::generateReference(), 'title' => 'API workflow fixture', 'status' => 'active',
            'priority' => 'medium', 'owner_id' => $admin->id, 'currency' => 'XAF',
        ]);

        $milestone = AdminWorkflowAdapter::record('admin.operations.milestones.create', [
            'title' => 'API milestone', 'description' => 'Fixture', 'due_date' => '2026-12-01', 'status' => 'pending',
        ], ['project' => $project->id]);
        $this->assertSame($project->id, $milestone->project_id);

        $listing = app(WorkflowDispatcher::class)->execute('admin.operations.milestones.index', new WorkflowInput(actor: $admin, parameters: ['project' => $project]));
        $this->assertSame($milestone->id, $listing->data['items'][0]['id']);
        AdminWorkflowAdapter::record('admin.operations.milestones.update', ['title' => 'Updated milestone', 'status' => 'in_progress'], ['project' => $project->id, 'milestone' => $milestone->id]);
        AdminWorkflowAdapter::run('admin.workforce.milestones.complete', parameters: ['milestone' => $milestone->id]);
        $this->assertDatabaseHas('milestones', ['id' => $milestone->id, 'status' => 'completed']);

        $this->expectException(ValidationException::class);
        AdminWorkflowAdapter::run('admin.operations.milestones.delete', parameters: ['project' => $project->id, 'milestone' => $milestone->id]);
    }
}
