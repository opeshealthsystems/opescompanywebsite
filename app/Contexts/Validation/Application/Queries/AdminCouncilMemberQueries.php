<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\AdvisoryCouncilMember;
use Illuminate\Database\Eloquent\Builder;

final class AdminCouncilMemberQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(AdvisoryCouncilMember $member): array
    {
        return [
            'id' => $member->id, 'user_id' => $member->user_id, 'member_name' => $member->user?->name,
            'validation_certificate_id' => $member->validation_certificate_id, 'title' => $member->title,
            'term_start' => $member->term_start?->toDateString(), 'term_end' => $member->term_end?->toDateString(),
            'status' => $member->status, 'invited_by' => $member->invited_by,
            'invited_by_name' => $member->invitedBy?->name, 'invited_at' => $member->invited_at?->toISOString(),
        ];
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return AdvisoryCouncilMember::query()->with(['user:id,name', 'invitedBy:id,name'])->orderByDesc('invited_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $page = $input->paginate($this->query($input));
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (AdvisoryCouncilMember $member) => $this->item($member))->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, AdvisoryCouncilMember $member): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['member' => $this->item($member->load(['user:id,name', 'invitedBy:id,name']))]);
    }
}
