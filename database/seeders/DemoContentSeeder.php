<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\PartnerInstitution;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\PractitionerProfile;
use App\Models\PractitionerProgram;
use App\Models\ServiceRequest;
use App\Models\Suggestion;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $practitioner = $this->seedPractitioner();
        $this->seedFindings($practitioner);
        $this->seedPrograms();
        $this->seedSurveys();
        $this->seedCourses();
        $this->seedPartners();
        $this->seedSuggestions($practitioner);
        $this->seedServiceRequests();
    }

    private function seedPractitioner(): User
    {
        $user = User::firstOrCreate(
            ['email' => 'dr.demo@opes.test'],
            ['name' => 'Dr. Amina Demo', 'password' => Hash::make('demo1234')]
        );
        $user->syncRoles(['practitioner']);

        PractitionerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'profession'          => 'doctor',
                'specialty'           => 'Internal Medicine',
                'workplace_name'      => 'Yaoundé Central Hospital',
                'workplace_city'      => 'Yaoundé',
                'workplace_country'   => 'CM',
                'registration_number' => 'REG-2024-AD01',
                'years_of_experience' => 12,
                'bio'                 => 'Internal medicine physician focused on digital health adoption in tertiary care settings.',
                'opes_testimonial'    => 'OPES health information systems cut our patient wait times dramatically and gave our department reliable, real-time data we can finally trust.',
                'is_verified'         => true,
            ]
        );

        $this->command->info('Seeded demo practitioner dr.demo@opes.test with verified profile.');

        return $user;
    }

    private function seedFindings(User $practitioner): void
    {
        $program = PractitionerProgram::firstOrCreate(
            ['title' => 'OPES Clinical Pilot Evaluation'],
            [
                'description' => 'Field evaluation program for practitioners testing OPES products in live clinical settings.',
                'type'        => 'volunteer',
                'status'      => 'open',
                'starts_at'   => now()->subMonth(),
                'ends_at'     => now()->addMonths(2),
            ]
        );

        $application = PractitionerApplication::firstOrCreate(
            ['practitioner_id' => $practitioner->id, 'program_id' => $program->id],
            [
                'motivation'  => 'Eager to evaluate OPES tools in a high-volume internal medicine ward.',
                'status'      => 'approved',
                'reviewed_at' => now()->subWeeks(3),
            ]
        );

        $findings = [
            [
                'findings_text'         => 'After two weeks of use, patient registration time dropped by nearly half. Staff onboarding was intuitive and data entry errors fell noticeably.',
                'wait_time_rating'      => 5,
                'data_integrity_rating' => 5,
                'usability_rating'      => 4,
                'overall_rating'        => 5,
                'video_url'             => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ],
            [
                'findings_text'         => 'The reporting dashboard gave us reliable real-time bed-occupancy data. Integration with our lab system was smooth and the audit trail is excellent for compliance.',
                'wait_time_rating'      => 4,
                'data_integrity_rating' => 5,
                'usability_rating'      => 5,
                'overall_rating'        => 5,
                'video_url'             => 'https://vimeo.com/76979871',
            ],
            [
                'findings_text'         => 'Mobile access let our on-call doctors review charts remotely. Minor latency on slow connections, but overall a strong improvement over our paper workflow.',
                'wait_time_rating'      => 4,
                'data_integrity_rating' => 4,
                'usability_rating'      => 4,
                'overall_rating'        => 4,
                'video_url'             => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
            ],
        ];

        foreach ($findings as $data) {
            PractitionerFinding::firstOrCreate(
                [
                    'application_id'  => $application->id,
                    'practitioner_id' => $practitioner->id,
                    'findings_text'   => $data['findings_text'],
                ],
                array_merge($data, ['is_published' => true])
            );
        }

        $this->command->info('Seeded 3 published practitioner findings.');
    }

    private function seedPrograms(): void
    {
        PractitionerProgram::firstOrCreate(
            ['title' => 'OPES Community Health Volunteer Drive'],
            [
                'description'      => 'Volunteer practitioners help validate OPES community health screening modules across rural clinics.',
                'type'             => 'volunteer',
                'status'           => 'open',
                'compensation'     => null,
                'max_participants' => 20,
                'starts_at'        => now()->addWeeks(2),
                'ends_at'          => now()->addMonths(4),
            ]
        );

        PractitionerProgram::firstOrCreate(
            ['title' => 'OPES Paid Clinical Research Panel'],
            [
                'description'      => 'Compensated panel of practitioners providing structured feedback on OPES diagnostic reporting tools.',
                'type'             => 'paid',
                'status'           => 'open',
                'compensation'     => '250,000 XAF',
                'max_participants' => 10,
                'starts_at'        => now()->addWeeks(3),
                'ends_at'          => now()->addMonths(3),
            ]
        );

        $this->command->info('Seeded 2 practitioner programs (1 volunteer, 1 paid).');
    }

    private function seedSurveys(): void
    {
        $practitionerSurvey = Survey::firstOrCreate(
            ['title' => 'Practitioner Experience with OPES Systems'],
            [
                'description' => 'Help us understand how OPES products perform in your daily clinical practice.',
                'audience'    => 'practitioners',
                'status'      => 'active',
            ]
        );

        $this->seedQuestions($practitionerSurvey, [
            ['question' => 'How would you rate the overall usability of OPES systems?', 'type' => 'rating', 'is_required' => true],
            ['question' => 'Which OPES module do you use most often?', 'type' => 'multiple_choice', 'is_required' => true, 'options' => ['Patient Registration', 'Reporting Dashboard', 'Lab Integration', 'Mobile Charting']],
            ['question' => 'Has OPES reduced your administrative workload?', 'type' => 'yes_no', 'is_required' => true],
            ['question' => 'What improvements would you like to see?', 'type' => 'text', 'is_required' => false],
        ]);

        $generalSurvey = Survey::firstOrCreate(
            ['title' => 'OPES General Satisfaction Survey'],
            [
                'description' => 'A short survey for all users about their experience with OPES Health Systems.',
                'audience'    => 'all',
                'status'      => 'active',
            ]
        );

        $this->seedQuestions($generalSurvey, [
            ['question' => 'How satisfied are you with OPES overall?', 'type' => 'rating', 'is_required' => true],
            ['question' => 'Would you recommend OPES to a colleague?', 'type' => 'yes_no', 'is_required' => true],
            ['question' => 'Any additional comments?', 'type' => 'text', 'is_required' => false],
        ]);

        $this->command->info('Seeded 2 active surveys with questions.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     */
    private function seedQuestions(Survey $survey, array $questions): void
    {
        foreach ($questions as $index => $q) {
            SurveyQuestion::firstOrCreate(
                ['survey_id' => $survey->id, 'question' => $q['question']],
                [
                    'type'        => $q['type'],
                    'options'     => $q['options'] ?? null,
                    'is_required' => $q['is_required'],
                    'sort_order'  => $index,
                ]
            );
        }
    }

    private function seedCourses(): void
    {
        $courses = [
            [
                'title'       => 'Getting Started with OPES Health Information Systems',
                'description' => 'A beginner-friendly introduction to navigating OPES products and core workflows in a clinical environment.',
                'level'       => 'beginner',
                'duration'    => 4,
                'featured'    => true,
                'lessons'     => [
                    ['title' => 'Welcome to OPES', 'minutes' => 12],
                    ['title' => 'Logging In and Navigating the Dashboard', 'minutes' => 18],
                    ['title' => 'Registering Your First Patient', 'minutes' => 22],
                    ['title' => 'Understanding User Roles and Permissions', 'minutes' => 15],
                ],
            ],
            [
                'title'       => 'Mastering OPES Clinical Reporting',
                'description' => 'Learn to build, interpret, and export clinical reports using the OPES reporting dashboard.',
                'level'       => 'intermediate',
                'duration'    => 6,
                'featured'    => false,
                'lessons'     => [
                    ['title' => 'Report Templates Overview', 'minutes' => 20],
                    ['title' => 'Building a Custom Report', 'minutes' => 30],
                    ['title' => 'Scheduling and Exporting Reports', 'minutes' => 18],
                ],
            ],
            [
                'title'       => 'OPES Data Integrity and Compliance',
                'description' => 'Advanced guidance on maintaining data integrity, audit trails, and regulatory compliance in OPES.',
                'level'       => 'advanced',
                'duration'    => 5,
                'featured'    => false,
                'lessons'     => [
                    ['title' => 'Audit Trails Explained', 'minutes' => 24],
                    ['title' => 'Data Validation Rules', 'minutes' => 26],
                    ['title' => 'Compliance Checklists', 'minutes' => 20],
                    ['title' => 'Incident Reporting Workflow', 'minutes' => 22],
                ],
            ],
        ];

        foreach ($courses as $sortOrder => $courseData) {
            $course = Course::firstOrCreate(
                ['slug' => Str::slug($courseData['title'])],
                [
                    'title'          => $courseData['title'],
                    'description'    => $courseData['description'],
                    'level'          => $courseData['level'],
                    'duration_hours' => $courseData['duration'],
                    'is_active'      => true,
                    'is_featured'    => $courseData['featured'],
                    'sort_order'     => $sortOrder,
                ]
            );

            foreach ($courseData['lessons'] as $lessonOrder => $lesson) {
                CourseLesson::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $lesson['title']],
                    [
                        'content'          => 'In this lesson you will learn about ' . strtolower($lesson['title']) . ' within the OPES platform, including practical hands-on steps.',
                        'video_url'        => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => $lesson['minutes'],
                        'sort_order'       => $lessonOrder,
                    ]
                );
            }
        }

        $this->command->info('Seeded 3 active courses with ordered lessons.');
    }

    private function seedPartners(): void
    {
        $partners = [
            ['name' => 'University of Yaoundé I', 'type' => 'university', 'city' => 'Yaoundé', 'since' => 2018, 'featured' => true],
            ['name' => 'Centre for Health Research Cameroon', 'type' => 'research_institute', 'city' => 'Douala', 'since' => 2019, 'featured' => true],
            ['name' => 'HealthBridge NGO', 'type' => 'ngo', 'city' => 'Bamenda', 'since' => 2020, 'featured' => true],
            ['name' => 'University of Buea Faculty of Health Sciences', 'type' => 'university', 'city' => 'Buea', 'since' => 2021, 'featured' => false],
            ['name' => 'Pan-African Digital Health Institute', 'type' => 'research_institute', 'city' => 'Yaoundé', 'since' => 2022, 'featured' => true],
        ];

        foreach ($partners as $sortOrder => $partner) {
            PartnerInstitution::firstOrCreate(
                ['name' => $partner['name']],
                [
                    'type'              => $partner['type'],
                    'country'           => 'CM',
                    'city'              => $partner['city'],
                    'website'           => 'https://example.com/' . Str::slug($partner['name']),
                    'description'       => $partner['name'] . ' collaborates with OPES Health Systems on digital health research and deployment.',
                    'partnership_since' => $partner['since'],
                    'is_featured'       => $partner['featured'],
                    'is_active'         => true,
                    'sort_order'        => $sortOrder,
                ]
            );
        }

        $this->command->info('Seeded 5 partner institutions.');
    }

    private function seedSuggestions(User $practitioner): void
    {
        Suggestion::firstOrCreate(
            ['user_id' => $practitioner->id, 'title' => 'Add offline mode for rural clinics'],
            [
                'category' => 'feature_request',
                'body'     => 'Many rural clinics have intermittent connectivity. An offline mode that syncs when back online would be hugely valuable.',
                'status'   => 'pending',
            ]
        );

        Suggestion::firstOrCreate(
            ['user_id' => $practitioner->id, 'title' => 'Improve the lab results layout'],
            [
                'category'       => 'improvement',
                'body'           => 'The lab results screen could group values by panel and highlight out-of-range results more clearly.',
                'status'         => 'accepted',
                'admin_response' => 'Thank you for the feedback. A redesigned lab results layout with panel grouping is planned for the next release.',
                'responded_at'   => now()->subDays(5),
            ]
        );

        $this->command->info('Seeded 2 suggestions (1 pending, 1 responded).');
    }

    private function seedServiceRequests(): void
    {
        $customer = User::where('email', 'customer@demo.opes')->first();

        if (! $customer) {
            $customer = User::firstOrCreate(
                ['email' => 'facility.demo@opes.test'],
                ['name' => 'Demo Facility', 'password' => Hash::make('demo1234')]
            );
            $customer->syncRoles(['customer']);
        }

        ServiceRequest::firstOrCreate(
            ['customer_id' => $customer->id, 'type' => 'installation', 'description' => 'Initial installation of OPES patient registration module at our main facility.'],
            [
                'preferred_date' => now()->addWeeks(2)->toDateString(),
                'location'       => 'Main Reception, Yaoundé Central Hospital',
                'status'         => 'pending',
            ]
        );

        ServiceRequest::firstOrCreate(
            ['customer_id' => $customer->id, 'type' => 'training', 'description' => 'On-site staff training for the OPES reporting dashboard.'],
            [
                'preferred_date' => now()->addWeeks(3)->toDateString(),
                'location'       => 'Training Room B, Douala Branch',
                'status'         => 'confirmed',
                'confirmed_date' => now()->addWeeks(3)->addDay()->toDateString(),
            ]
        );

        $this->command->info('Seeded 2 service requests (1 pending, 1 confirmed).');
    }
}
