<?php

namespace App\Filament\Resources\DealResource\Pages;

use App\Filament\Resources\DealResource;
use App\Models\Deal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDeal extends EditRecord
{
    protected static string $resource = DealResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()
            ->action(fn (Deal $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.crm.deals.delete', parameters: ['deal' => $record->id]))
            ->hidden(fn () => in_array($this->record->stage, ['closed_won', 'closed_lost'], true))];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.crm.deals.update', $data, ['deal' => $record]);
    }
}
