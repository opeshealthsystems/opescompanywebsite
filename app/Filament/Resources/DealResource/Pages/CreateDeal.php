<?php

namespace App\Filament\Resources\DealResource\Pages;

use App\Filament\Resources\DealResource;
use App\Models\Deal;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDeal extends CreateRecord
{
    protected static string $resource = DealResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.crm.deals.create',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, actor: auth()->user(), language: app()->getLocale()),
        );

        return Deal::query()->findOrFail($result->data['id']);
    }
}
