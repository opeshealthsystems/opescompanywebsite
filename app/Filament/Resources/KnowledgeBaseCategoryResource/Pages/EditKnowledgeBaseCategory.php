<?php
namespace App\Filament\Resources\KnowledgeBaseCategoryResource\Pages;
use App\Filament\Resources\KnowledgeBaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditKnowledgeBaseCategory extends EditRecord {
    protected static string $resource = KnowledgeBaseCategoryResource::class;
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.support.knowledgeCategories.update', $data, ['category' => $record->id]);
    }
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()->using(function ($record) {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.support.knowledgeCategories.delete', parameters: ['category' => $record->id]);
        return true;
    })]; }
}
