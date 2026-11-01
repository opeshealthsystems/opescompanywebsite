<?php

namespace App\Filament\Resources\FinalEvaluationResource\Pages;

use App\Filament\Resources\FinalEvaluationResource;
use Filament\Resources\Pages\EditRecord;

class EditFinalEvaluation extends EditRecord
{
    protected static string $resource = FinalEvaluationResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.validation.finalEvaluations.update',
            new \App\Contexts\Shared\Application\WorkflowInput($data, ['evaluation' => $record], auth()->user(), app()->getLocale()),
        );
        return $record->fresh();
    }
}
