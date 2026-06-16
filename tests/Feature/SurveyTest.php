<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SurveyTest extends TestCase
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

    private function customer(): User
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $user->customerProfile()->create(['country' => 'CM']);
        return $user;
    }

    // ── Audience filtering ────────────────────────────────────────────────

    public function test_practitioner_index_filters_by_audience(): void
    {
        $practitioner = $this->practitioner();

        $forPractitioners = Survey::factory()->forPractitioners()->create(['title' => 'Practitioner Feedback Survey']);
        $forCustomers     = Survey::factory()->forCustomers()->create(['title' => 'Customer Only Survey']);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/surveys')
            ->assertOk()
            ->assertSee('Practitioner Feedback Survey')
            ->assertDontSee('Customer Only Survey');
    }

    public function test_customer_index_filters_by_audience(): void
    {
        $customer = $this->customer();

        $forCustomers     = Survey::factory()->forCustomers()->create(['title' => 'Customer Feedback Survey']);
        $forAll           = Survey::factory()->create(['audience' => 'all', 'title' => 'Everybody Survey']);
        $forPractitioners = Survey::factory()->forPractitioners()->create(['title' => 'Practitioner Only Survey']);

        $this->actingAs($customer)
            ->get('/en/customer/surveys')
            ->assertOk()
            ->assertSee('Customer Feedback Survey')
            ->assertSee('Everybody Survey')
            ->assertDontSee('Practitioner Only Survey');
    }

    // ── Show ──────────────────────────────────────────────────────────────

    public function test_show_creates_a_draft_response_and_renders_questions(): void
    {
        $practitioner = $this->practitioner();

        $survey = Survey::factory()->forPractitioners()->create();
        $question = SurveyQuestion::factory()->create([
            'survey_id' => $survey->id,
            'question'  => 'How would you rate the platform?',
        ]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/surveys/' . $survey->id)
            ->assertOk()
            ->assertSee('How would you rate the platform?');

        $this->assertDatabaseHas('survey_responses', [
            'survey_id'    => $survey->id,
            'user_id'      => $practitioner->id,
            'submitted_at' => null,
        ]);
    }

    // ── Submit ────────────────────────────────────────────────────────────

    public function test_submit_stores_answers_for_each_question_type(): void
    {
        $practitioner = $this->practitioner();

        $survey = Survey::factory()->forPractitioners()->create();

        $text   = SurveyQuestion::factory()->create(['survey_id' => $survey->id, 'type' => 'text', 'sort_order' => 1]);
        $rating = SurveyQuestion::factory()->rating()->create(['survey_id' => $survey->id, 'sort_order' => 2]);
        $choice = SurveyQuestion::factory()->multipleChoice()->create(['survey_id' => $survey->id, 'options' => ['Alpha', 'Beta'], 'sort_order' => 3]);
        $yesNo  = SurveyQuestion::factory()->yesNo()->create(['survey_id' => $survey->id, 'sort_order' => 4]);

        $this->actingAs($practitioner)
            ->post('/en/practitioner/surveys/' . $survey->id, [
                "q_{$text->id}"   => 'Great experience',
                "q_{$rating->id}" => 4,
                "q_{$choice->id}" => 'Beta',
                "q_{$yesNo->id}"  => 'yes',
            ])
            ->assertRedirect();

        $response = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $practitioner->id)
            ->first();

        $this->assertNotNull($response->submitted_at);

        $this->assertDatabaseHas('survey_answers', [
            'response_id' => $response->id,
            'question_id' => $text->id,
            'answer_text' => 'Great experience',
        ]);
        $this->assertDatabaseHas('survey_answers', [
            'response_id'   => $response->id,
            'question_id'   => $rating->id,
            'answer_rating' => 4,
        ]);
        $this->assertDatabaseHas('survey_answers', [
            'response_id'   => $response->id,
            'question_id'   => $choice->id,
            'answer_choice' => 'Beta',
        ]);
        $this->assertDatabaseHas('survey_answers', [
            'response_id' => $response->id,
            'question_id' => $yesNo->id,
            'answer_bool' => true,
        ]);
    }

    public function test_required_question_blank_redirects_back_with_error_and_does_not_submit(): void
    {
        $practitioner = $this->practitioner();

        $survey   = Survey::factory()->forPractitioners()->create();
        $question = SurveyQuestion::factory()->create([
            'survey_id'   => $survey->id,
            'type'        => 'text',
            'is_required' => true,
        ]);

        $this->actingAs($practitioner)
            ->post('/en/practitioner/surveys/' . $survey->id, [
                "q_{$question->id}" => '',
            ])
            ->assertSessionHasErrors("q_{$question->id}");

        $this->assertDatabaseMissing('survey_responses', [
            'survey_id' => $survey->id,
            'user_id'   => $practitioner->id,
            'submitted_at' => now(),
        ]);

        $response = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $practitioner->id)
            ->first();
        $this->assertNull($response->submitted_at);
        $this->assertDatabaseCount('survey_answers', 0);
    }

    public function test_already_submitted_survey_cannot_be_resubmitted(): void
    {
        $practitioner = $this->practitioner();

        $survey   = Survey::factory()->forPractitioners()->create();
        $question = SurveyQuestion::factory()->create([
            'survey_id' => $survey->id,
            'type'      => 'text',
        ]);

        $response = SurveyResponse::factory()->submitted()->create([
            'survey_id' => $survey->id,
            'user_id'   => $practitioner->id,
        ]);
        $submittedAt = $response->submitted_at;

        $this->actingAs($practitioner)
            ->post('/en/practitioner/surveys/' . $survey->id, [
                "q_{$question->id}" => 'Late answer',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('survey_answers', 0);

        $response->refresh();
        $this->assertEquals(
            $submittedAt->timestamp,
            $response->submitted_at->timestamp
        );
    }

    // ── Status guard ──────────────────────────────────────────────────────

    public function test_draft_survey_is_not_listed_on_index(): void
    {
        $practitioner = $this->practitioner();

        Survey::factory()->forPractitioners()->draft()->create(['title' => 'Hidden Draft Survey']);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/surveys')
            ->assertOk()
            ->assertDontSee('Hidden Draft Survey');
    }

    public function test_practitioner_cannot_access_customer_only_survey(): void
    {
        $practitioner = $this->practitioner();

        $survey = Survey::factory()->forCustomers()->create();

        $this->actingAs($practitioner)
            ->get('/en/practitioner/surveys/' . $survey->id)
            ->assertForbidden();
    }
}
