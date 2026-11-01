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
        $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.validation.finalEvaluations.create',
            new \App\Contexts\Shared\Application\WorkflowInput($data, [], auth()->user(), app()->getLocale()),
        );
        return FinalEvaluation::findOrFail($result->data['id']);
    }
}
