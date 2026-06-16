<?php

namespace Tests\Feature;

use App\Mail\SuggestionResponded;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SuggestionTest extends TestCase
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
        return $user;
    }

    public function test_practitioner_can_submit_a_suggestion(): void
    {
        $practitioner = $this->practitioner();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/suggestions', [
                'title'    => 'Add dark mode to the dashboard',
                'category' => 'feature_request',
                'body'     => 'It would be great to have a dark mode option for late-night work sessions.',
            ])
            ->assertRedirect('/en/practitioner/suggestions')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('suggestions', [
            'title'    => 'Add dark mode to the dashboard',
            'category' => 'feature_request',
            'user_id'  => $practitioner->id,
            'status'   => 'pending',
        ]);
    }

    public function test_validation_rejects_empty_title(): void
    {
        $practitioner = $this->practitioner();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/suggestions', [
                'title'    => '',
                'category' => 'feature_request',
                'body'     => 'A perfectly reasonable body with enough characters here.',
            ])
            ->assertSessionHasErrors('title');

        $this->assertDatabaseCount('suggestions', 0);
    }

    public function test_validation_rejects_too_short_body(): void
    {
        $practitioner = $this->practitioner();

        $this->actingAs($practitioner)
            ->post('/en/practitioner/suggestions', [
                'title'    => 'Short body suggestion',
                'category' => 'improvement',
                'body'     => 'too short',
            ])
            ->assertSessionHasErrors('body');

        $this->assertDatabaseCount('suggestions', 0);
    }

    public function test_index_shows_only_the_authenticated_practitioners_suggestions(): void
    {
        $practitioner = $this->practitioner();
        $other        = $this->practitioner();

        $mine   = Suggestion::factory()->create([
            'user_id' => $practitioner->id,
            'title'   => 'My very own suggestion',
        ]);
        $theirs = Suggestion::factory()->create([
            'user_id' => $other->id,
            'title'   => 'Somebody elses private idea',
        ]);

        $this->actingAs($practitioner)
            ->get('/en/practitioner/suggestions')
            ->assertOk()
            ->assertSee($mine->title)
            ->assertDontSee($theirs->title);
    }

    public function test_responding_to_a_suggestion_updates_it_and_queues_mail(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $practitioner = $this->practitioner();

        $suggestion = Suggestion::factory()->create([
            'user_id' => $practitioner->id,
            'status'  => 'pending',
        ]);

        $suggestion->update([
            'status'         => 'accepted',
            'admin_response' => 'Great idea, we have added this to our roadmap.',
            'responded_by'   => $admin->id,
            'responded_at'   => now(),
        ]);

        Mail::to($suggestion->user->email)->queue(new SuggestionResponded($suggestion));

        $this->assertDatabaseHas('suggestions', [
            'id'           => $suggestion->id,
            'status'       => 'accepted',
            'responded_by' => $admin->id,
        ]);

        Mail::assertQueued(SuggestionResponded::class, function ($mail) use ($suggestion) {
            return $mail->suggestion->id === $suggestion->id;
        });
    }
}
