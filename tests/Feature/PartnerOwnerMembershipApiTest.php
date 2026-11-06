<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\PartnerApplication;
use App\Models\PartnerApplicationMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PartnerOwnerMembershipApiTest extends TestCase
{
    use RefreshDatabase;

    private function partner(string $email): User
    {
        $user = User::factory()->create([
            'email' => $email,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('partner');

        return $user;
    }

    private function application(): PartnerApplication
    {
        return PartnerApplication::query()->create([
            'organization_name' => 'Fictional partner network',
            'contact_name' => 'Fictional contact',
            'email' => 'network@example.test',
            'country' => 'Cameroon',
            'partner_type' => 'technology',
            'description' => 'Fictional fixture for API workflow validation.',
            'status' => 'approved',
        ]);
    }

    public function test_organization_owner_can_list_add_and_remove_members_idempotently_without_losing_last_owner(): void
    {
        Role::findOrCreate('partner', 'web');
        $owner = $this->partner('owner@example.test');
        $newOwner = $this->partner('new-owner@example.test');
        $application = $this->application();
        $firstMembership = PartnerApplicationMembership::query()->create([
            'partner_application_id' => $application->id,
            'user_id' => $owner->id,
            'membership_role' => 'owner',
        ]);
        $dispatcher = app(WorkflowDispatcher::class);

        $read = $dispatcher->execute('partner.accounts.members.index', new WorkflowInput(
            parameters: ['partnerApplication' => $application->id], actor: $owner, api: true, scopes: ['partner:read'],
        ));
        $this->assertSame([$owner->id], array_column($read->data['items'], 'user_id'));

        $addInput = new WorkflowInput(
            values: ['email' => $newOwner->email, 'membership_role' => 'owner'],
            parameters: ['partnerApplication' => $application->id],
            actor: $owner,
            api: true,
            scopes: ['partner:write'],
        );
        $added = $dispatcher->execute('partner.accounts.members.add', $addInput, 'partner-owner-add-0001');
        $replayed = $dispatcher->execute('partner.accounts.members.add', $addInput, 'partner-owner-add-0001');
        $this->assertSame($added->data['membership_id'], $replayed->data['membership_id']);
        $this->assertSame(2, PartnerApplicationMembership::query()->where('partner_application_id', $application->id)->count());

        $dispatcher->execute('partner.accounts.members.remove', new WorkflowInput(
            parameters: ['partnerApplication' => $application->id, 'member' => $owner->id],
            actor: $newOwner,
            api: true,
            scopes: ['partner:write'],
        ), 'partner-owner-remove-0001');
        $this->assertDatabaseMissing('partner_application_memberships', ['id' => $firstMembership->id]);

        try {
            $dispatcher->execute('partner.accounts.members.remove', new WorkflowInput(
                parameters: ['partnerApplication' => $application->id, 'member' => $newOwner->id],
                actor: $newOwner,
                api: true,
                scopes: ['partner:write'],
            ), 'partner-owner-remove-0002');
            $this->fail('The final organization owner must not be removable.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('partner_application_memberships', [
                'partner_application_id' => $application->id,
                'user_id' => $newOwner->id,
                'membership_role' => 'owner',
            ]);
        }
    }

    public function test_partner_without_owner_membership_cannot_read_organization_members(): void
    {
        Role::findOrCreate('partner', 'web');
        $partner = $this->partner('unlinked@example.test');
        $application = $this->application();

        $this->expectException(HttpException::class);
        app(WorkflowDispatcher::class)->execute('partner.accounts.members.index', new WorkflowInput(
            parameters: ['partnerApplication' => $application->id], actor: $partner, api: true, scopes: ['partner:read'],
        ));
    }
}
