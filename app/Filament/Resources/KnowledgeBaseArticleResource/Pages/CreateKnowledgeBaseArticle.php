<?php
namespace App\Filament\Resources\KnowledgeBaseArticleResource\Pages;
use App\Filament\Resources\KnowledgeBaseArticleResource;
use Filament\Resources\Pages\CreateRecord;
class CreateKnowledgeBaseArticle extends CreateRecord {
    protected static string $resource = KnowledgeBaseArticleResource::class;
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.support.knowledgeArticles.create', $data);
    }
}
