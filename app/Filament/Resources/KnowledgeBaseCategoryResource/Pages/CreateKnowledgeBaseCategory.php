<?php
namespace App\Filament\Resources\KnowledgeBaseCategoryResource\Pages;
use App\Filament\Resources\KnowledgeBaseCategoryResource;
use Filament\Resources\Pages\CreateRecord;
class CreateKnowledgeBaseCategory extends CreateRecord {
    protected static string $resource = KnowledgeBaseCategoryResource::class;
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.support.knowledgeCategories.create', $data);
    }
}
