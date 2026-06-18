<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    private function makeSurveyWithQuestion(string $audience, string $type, array $options = []): array
    {
        $survey = Survey::create([
            'title'    => 'Test Survey',
            'status'   => 'active',
            'audience' => $audience,
        ]);
        $question = SurveyQuestion::create([
            'survey_id'   => $survey->id,
            'type'        => $type,
            'question'    => 'Test question',
            'is_required' => true,
            'options'     => $options ?: null,
            'sort_order'  => 1,
        ]);
        return [$survey, $question];
    }

    public function test_customer_survey_rejects_out_of_bounds_rating(): void
    {
        $customer = $this->makeUser('customer');
        [$survey, $question] = $this->makeSurveyWithQuestion('customers', 'rating');

        $this->actingAs($customer)
            ->post("/en/customer/surveys/{$survey->id}", [
                "q_{$question->id}" => 6,
            ])
            ->assertSessionHasErrors("q_{$question->id}");
    }

    public function test_customer_survey_rejects_invalid_multiple_choice(): void
    {
        $customer = $this->makeUser('customer');
        [$survey, $question] = $this->makeSurveyWithQuestion('customers', 'multiple_choice', ['good', 'bad']);

        $this->actingAs($customer)
            ->post("/en/customer/surveys/{$survey->id}", [
                "q_{$question->id}" => 'hacked_value',
            ])
            ->assertSessionHasErrors("q_{$question->id}");
    }

    public function test_customer_survey_accepts_valid_rating(): void
    {
        $customer = $this->makeUser('customer');
        [$survey, $question] = $this->makeSurveyWithQuestion('customers', 'rating');

        $response = $this->actingAs($customer)->post("/en/customer/surveys/{$survey->id}", [
            "q_{$question->id}" => 4,
        ]);
        // Should redirect (success) not fail validation
        $response->assertSessionDoesntHaveErrors();
    }

    public function test_practitioner_survey_rejects_out_of_bounds_rating(): void
    {
        $practitioner = $this->makeUser('practitioner');
        [$survey, $question] = $this->makeSurveyWithQuestion('practitioners', 'rating');

        $this->actingAs($practitioner)
            ->post("/en/practitioner/surveys/{$survey->id}", [
                "q_{$question->id}" => 0,
            ])
            ->assertSessionHasErrors("q_{$question->id}");
    }

    public function test_practitioner_survey_rejects_invalid_yes_no(): void
    {
        $practitioner = $this->makeUser('practitioner');
        [$survey, $question] = $this->makeSurveyWithQuestion('practitioners', 'yes_no');

        $this->actingAs($practitioner)
            ->post("/en/practitioner/surveys/{$survey->id}", [
                "q_{$question->id}" => 'maybe',
            ])
            ->assertSessionHasErrors("q_{$question->id}");
    }
}
