<?php

namespace App\Filament\Resources\FinalEvaluationResource\Pages;

use App\Filament\Resources\FinalEvaluationResource;
use App\Models\FinalEvaluation;
use Filament\Resources\Pages\CreateRecord;

class CreateFinalEvaluation extends CreateRecord
{
    protected static string $resource = FinalEvaluationResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.finalEvaluations.create', $data);
    }
}
