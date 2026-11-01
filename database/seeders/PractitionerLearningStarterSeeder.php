<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Small, non-clinical starter curriculum for the practitioner mobile learning flow. */
class PractitionerLearningStarterSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->course(
                'safe-digital-health-records',
                'Safe digital health records',
                'Foundational learning on accurate records, privacy-aware access and reliable team handovers. This course covers digital information practice; it is not clinical guidance or accredited continuing education.',
                1,
                [
                    [
                        'title' => 'A reliable patient record',
                        'duration_minutes' => 8,
                        'content' => "A useful digital health record is accurate, timely and attributable. Enter information in the correct record, identify its source, and follow your organization’s approved process when correcting an entry. Do not create a second record to work around a possible duplicate.\n\nBefore saving, confirm that you have the right person and review required fields for completeness. If something is unclear, ask the appropriate record owner instead of guessing.",
                    ],
                    [
                        'title' => 'Protecting health information',
                        'duration_minutes' => 9,
                        'content' => "Use your own account and keep sign-in credentials private. Lock your screen when you step away. Access only the information needed for your assigned work, and avoid discussing identifiable information where others can overhear.\n\nIf information is sent to the wrong person or device, report it promptly through your organization’s incident process. Follow local policy for access, retention and disclosure.",
                    ],
                    [
                        'title' => 'Safe handover and continuity',
                        'duration_minutes' => 7,
                        'content' => "A clear digital handover states what changed, what follow-up is needed, who is responsible and when action is due. Use the approved fields so the next team member can find the information.\n\nThis lesson supports information practice only. Use your professional judgement and follow your workplace’s clinical policies for care decisions.",
                    ],
                ],
            );

            $this->course(
                'health-service-data-quality',
                'Data quality for health teams',
                'Practical study on completeness, consistency and review habits for service records and reports. This course does not provide clinical advice or accredited continuing-education credit.',
                2,
                [
                    [
                        'title' => 'Completeness and consistency',
                        'duration_minutes' => 10,
                        'content' => "Completeness means required information is present. Consistency means related values agree across records and reporting periods. Teams should use shared definitions and the same approved process for entering and correcting information.\n\nA regular review of missing fields can reveal where a workflow, form or staff guidance needs attention.",
                    ],
                    [
                        'title' => 'Reviewing a data-quality issue',
                        'duration_minutes' => 12,
                        'content' => "Start with the source record, identify the affected field and determine whether the issue is isolated or recurring. Correct an individual record through the approved process. If similar issues recur, address the underlying cause with a workflow or training improvement.\n\nKeep an audit trail of meaningful changes and follow your organization’s retention and access rules.",
                    ],
                    [
                        'title' => 'Using information responsibly',
                        'duration_minutes' => 9,
                        'content' => "Use information for its stated work purpose and share it only with people who need it for their role. Check the reporting period, definitions and source before comparing figures. State known limitations rather than filling gaps with assumptions.\n\nEscalate suspected errors through the relevant service or data-quality lead so that corrections can be reviewed and recorded.",
                    ],
                ],
            );
        });
    }

    /** @param array<int, array{title:string,duration_minutes:int,content:string}> $lessons */
    private function course(string $slug, string $title, string $description, int $order, array $lessons): void
    {
        $course = Course::firstOrCreate(
            ['slug' => $slug],
            [
                'title' => $title,
                'description' => $description,
                'level' => 'beginner',
                'duration_hours' => 1,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => $order,
            ],
        );

        foreach ($lessons as $index => $lesson) {
            $course->lessons()->firstOrCreate(
                ['title' => $lesson['title']],
                $lesson + ['sort_order' => $index + 1],
            );
        }
    }
}
