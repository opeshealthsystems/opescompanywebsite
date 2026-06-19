<?php

namespace App\Filament\Resources\FinalEvaluationResource\Pages;

use App\Filament\Resources\FinalEvaluationResource;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use Filament\Resources\Pages\CreateRecord;

class CreateFinalEvaluation extends CreateRecord
{
    protected static string $resource = FinalEvaluationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $member = CohortMember::findOrFail($data['cohort_member_id']);
        return array_merge($data, FinalEvaluation::snapshotData($member, auth()->id()));
    }
}
