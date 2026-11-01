<?php

namespace App\Filament\Resources\TesterApplicationResource\Pages;

use App\Filament\Resources\TesterApplicationResource;
use Filament\Resources\Pages\EditRecord;

class EditTesterApplication extends EditRecord
{
    protected static string $resource = TesterApplicationResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.enquiries.testerApplications.update', $data, ['testerApplication' => $record]);
        return $record->fresh();
    }
}
