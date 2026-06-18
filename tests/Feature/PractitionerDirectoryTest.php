<?php
namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProfile;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PractitionerDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function makePractitioner(array $profileOverrides = []): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        PractitionerProfile::create(array_merge([
            'user_id'             => $user->id,
            'profession'          => 'doctor',
            'specialty'           => 'Cardiology',
            'workplace_name'      => 'City Hospital',
            'workplace_country'   => 'Cameroon',
            'bio'                 => 'Experienced cardiologist.',
            'years_of_experience' => 10,
            'is_verified'         => false,
        ], $profileOverrides));
        return $user;
    }

    public function test_directory_lists_practitioners_with_approved_application(): void
    {
        $user    = $this->makePractitioner();
        $program = PractitionerProgram::create([
            'product_slug' => 'opes-clinic', 'product_name' => 'OPES Clinic',
            'title' => 'Pilot Review', 'type' => 'volunteer', 'status' => 'open',
        ]);
        PractitionerApplication::create([
            'practitioner_id' => $user->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
        ]);

        $this->get(route('practitioners.index', ['locale' => 'en']))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('Cardiology');
    }

    public function test_directory_excludes_practitioners_with_no_approved_application(): void
    {
        $user = $this->makePractitioner();

        $this->get(route('practitioners.index', ['locale' => 'en']))
            ->assertOk()
            ->assertDontSee($user->name);
    }

    public function test_directory_filters_by_profession(): void
    {
        $doctor = $this->makePractitioner(['profession' => 'doctor']);
        $nurse  = $this->makePractitioner(['profession' => 'nurse']);
        $program = PractitionerProgram::create([
            'product_slug' => 'opes-clinic', 'product_name' => 'OPES Clinic',
            'title' => 'Pilot', 'type' => 'volunteer', 'status' => 'open',
        ]);
        foreach ([$doctor, $nurse] as $u) {
            PractitionerApplication::create(['practitioner_id'=>$u->id,'program_id'=>$program->id,'status'=>'approved']);
        }

        $response = $this->get(route('practitioners.index', ['locale'=>'en','profession'=>'nurse']));
        $response->assertSee($nurse->name)->assertDontSee($doctor->name);
    }

    public function test_directory_does_not_expose_payout_number(): void
    {
        $user = $this->makePractitioner(['payout_number' => 'MOMO-12345']);
        $program = PractitionerProgram::create([
            'product_slug'=>'opes-clinic','product_name'=>'OPES Clinic','title'=>'Pilot','type'=>'volunteer','status'=>'open',
        ]);
        PractitionerApplication::create(['practitioner_id'=>$user->id,'program_id'=>$program->id,'status'=>'approved']);

        $this->get(route('practitioners.index', ['locale' => 'en']))
            ->assertDontSee('MOMO-12345');
    }
}
