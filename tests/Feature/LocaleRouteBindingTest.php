<?php

namespace Tests\Feature;

use App\Models\PractitionerProgram;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Regression tests for the locale-prefix route-model-binding bug: under
 * Route::prefix('{locale}'), a controller action whose model-bound parameter
 * is not preceded by a $locale parameter receives the locale string instead of
 * the model and 500s. These three actions were model-bound but untested, so the
 * bug slipped past the suite. Each assertion below fails before the $locale fix.
 */
class LocaleRouteBindingTest extends TestCase
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

    public function test_customer_can_open_a_survey_show_page(): void
    {
        $customer = $this->customer();
        $survey = Survey::factory()->forCustomers()->create(['title' => 'Facility Experience Survey']);
        SurveyQuestion::factory()->create(['survey_id' => $survey->id, 'type' => 'text', 'question' => 'How was your experience?']);

        $this->actingAs($customer)
            ->get("/en/customer/surveys/{$survey->id}")
            ->assertOk()
            ->assertSee('Facility Experience Survey');
    }

    public function test_customer_can_submit_a_survey(): void
    {
        $customer = $this->customer();
        $survey = Survey::factory()->forCustomers()->create();
        $question = SurveyQuestion::factory()->create([
            'survey_id'   => $survey->id,
            'type'        => 'text',
            'is_required' => true,
        ]);

        $this->actingAs($customer)
            ->post("/en/customer/surveys/{$survey->id}", ["q_{$question->id}" => 'Excellent platform'])
            ->assertRedirect();

        $this->assertDatabaseHas('survey_answers', [
            'question_id' => $question->id,
            'answer_text' => 'Excellent platform',
        ]);
    }

    public function test_practitioner_can_open_a_program_show_page(): void
    {
        $practitioner = $this->practitioner();
        $program = PractitionerProgram::factory()->create([
            'title'  => 'Radiology Module Beta Test',
            'status' => 'open',
        ]);

        $this->actingAs($practitioner)
            ->get("/en/practitioner/programs/{$program->id}")
            ->assertOk()
            ->assertSee('Radiology Module Beta Test');
    }
}
