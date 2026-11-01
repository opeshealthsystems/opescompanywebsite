<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\PractitionerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminPractitionerQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function page(WorkflowInput $input, mixed $query): WorkflowResult
    {
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->items(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function profileQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return PractitionerProfile::query()->with('user:id,name,email')->orderByDesc('id');
    }

    public function findingQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return PractitionerFinding::query()->with(['practitioner:id,name', 'application.program:id,title'])->orderByDesc('id');
    }

    public function applicationQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return PractitionerApplication::query()->with(['practitioner:id,name,email', 'program:id,title'])->orderByDesc('id');
    }

    public function profiles(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['profession' => 'sometimes|string|max:80', 'verified' => 'sometimes|boolean', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->profileQuery($input);
        if (isset($filters['profession'])) $query->where('profession', $filters['profession']);
        if (isset($filters['verified'])) $query->where('is_verified', $filters['verified']);
        if (!empty($filters['search'])) $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.trim($filters['search']).'%')->orWhere('email', 'like', '%'.trim($filters['search']).'%'));
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (PractitionerProfile $profile) => $this->profileData($profile))->all(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function profile(WorkflowInput $input, PractitionerProfile $profile): WorkflowResult
    {
        $this->admin($input); $profile->load('user:id,name,email');
        return WorkflowResult::payload(['profile' => $this->profileData($profile)]);
    }

    private function profileData(PractitionerProfile $profile): array
    {
        return $profile->only(['id', 'user_id', 'profession', 'specialty', 'workplace_name', 'workplace_city', 'workplace_country', 'years_of_experience', 'bio', 'opes_testimonial', 'is_verified', 'created_at', 'updated_at'])
            + ['user' => $profile->user?->only(['id', 'name', 'email'])];
    }

    public function findings(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['published' => 'sometimes|boolean', 'application_id' => 'sometimes|integer|exists:practitioner_applications,id']);
        $query = $this->findingQuery($input);
        if (isset($filters['published'])) $query->where('is_published', $filters['published']);
        if (isset($filters['application_id'])) $query->where('application_id', $filters['application_id']);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (PractitionerFinding $finding) => $this->findingData($finding))->all(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function finding(WorkflowInput $input, PractitionerFinding $finding): WorkflowResult
    {
        $this->admin($input); $finding->load(['practitioner:id,name', 'application.program:id,title']);
        return WorkflowResult::payload(['finding' => $this->findingData($finding)]);
    }

    private function findingData(PractitionerFinding $finding): array
    {
        return $finding->only(['id', 'application_id', 'practitioner_id', 'overall_rating', 'wait_time_rating', 'data_integrity_rating', 'usability_rating', 'findings_text', 'video_url', 'screenshot_path', 'is_published', 'created_at', 'updated_at'])
            + ['practitioner' => $finding->practitioner?->only(['id', 'name']), 'program' => $finding->application?->program?->only(['id', 'title'])];
    }

    public function applications(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:pending,approved,rejected,withdrawn', 'program_id' => 'sometimes|integer|exists:practitioner_programs,id']);
        $query = $this->applicationQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['program_id'])) $query->where('program_id', $filters['program_id']);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (PractitionerApplication $application) => $this->applicationData($application))->all(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function application(WorkflowInput $input, PractitionerApplication $application): WorkflowResult
    {
        $this->admin($input); $application->load(['practitioner:id,name,email', 'program:id,title']);
        return WorkflowResult::payload(['application' => $this->applicationData($application)]);
    }

    private function applicationData(PractitionerApplication $application): array
    {
        return $application->only(['id', 'practitioner_id', 'program_id', 'motivation', 'status', 'admin_notes', 'reviewed_by', 'reviewed_at', 'payout_status', 'payout_amount', 'payout_currency', 'payout_reference', 'payout_provider', 'payout_initiated_at', 'payout_failure_reason', 'paid_at', 'created_at', 'updated_at'])
            + ['practitioner' => $application->practitioner?->only(['id', 'name', 'email']), 'program' => $application->program?->only(['id', 'title'])];
    }
}
