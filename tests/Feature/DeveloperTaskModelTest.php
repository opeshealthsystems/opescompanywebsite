<?php

namespace Tests\Feature;

use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\Retest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeveloperTaskModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships_resolve(): void
    {
        $issue = IssueReport::factory()->create();
        $task  = DeveloperTask::factory()->create(['issue_report_id' => $issue->id]);
        $retest = Retest::factory()->create(['issue_report_id' => $issue->id, 'developer_task_id' => $task->id]);

        $this->assertEquals($task->id, $issue->fresh()->developerTask->id);
        $this->assertTrue($issue->fresh()->retests->contains($retest));
        $this->assertEquals($issue->id, $task->issueReport->id);
        $this->assertTrue($task->fresh()->retests->contains($retest));
        $this->assertEquals($task->id, $retest->developerTask->id);
    }

    public function test_new_statuses_and_option_maps(): void
    {
        $opts = IssueReport::statusOptions();
        $this->assertCount(13, $opts);
        $this->assertArrayHasKey('ready_for_retest', $opts);
        $this->assertArrayHasKey('retest_passed', $opts);
        $this->assertArrayHasKey('retest_failed', $opts);
        $this->assertCount(5, DeveloperTask::statusOptions());
        $this->assertCount(2, Retest::resultOptions());
    }
}
