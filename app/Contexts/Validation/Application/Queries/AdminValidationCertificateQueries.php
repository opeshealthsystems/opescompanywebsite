<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\User;
use App\Models\ValidationCertificate;
use Illuminate\Database\Eloquent\Builder;

final class AdminValidationCertificateQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function certificateQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return ValidationCertificate::query()->with(['cohortMember.user:id,name', 'cohortMember.cohort:id,name', 'issuedBy:id,name'])->orderByDesc('issued_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'tier' => 'sometimes|in:distinction,pass',
            'cohort_id' => 'sometimes|integer|exists:cohorts,id',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->certificateQuery($input);
        if (isset($filters['tier'])) $query->where('tier', $filters['tier']);
        if (isset($filters['cohort_id'])) $query->whereHas('cohortMember', fn (Builder $member) => $member->where('cohort_id', $filters['cohort_id']));
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn (Builder $builder) => $builder->where('certificate_number', 'like', '%'.$search.'%')
                ->orWhereHas('cohortMember.user', fn (Builder $user) => $user->where('name', 'like', '%'.$search.'%')));
        }
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (ValidationCertificate $certificate) => [
                'id' => $certificate->id,
                'cohort_member_id' => $certificate->cohort_member_id,
                'member_name' => $certificate->cohortMember?->user?->name,
                'cohort_id' => $certificate->cohortMember?->cohort_id,
                'cohort_name' => $certificate->cohortMember?->cohort?->name,
                'certificate_number' => $certificate->certificate_number,
                'score' => $certificate->score,
                'tier' => $certificate->tier,
                'issued_by' => $certificate->issued_by,
                'issuer_name' => $certificate->issuedBy?->name,
                'issued_at' => $certificate->issued_at?->toIso8601String(),
            ])->values()->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }
}
