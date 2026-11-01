<?php
namespace App\Filament\Resources\KnowledgeBaseArticleResource\Pages;
use App\Filament\Resources\KnowledgeBaseArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditKnowledgeBaseArticle extends EditRecord {
    protected static string $resource = KnowledgeBaseArticleResource::class;
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.support.knowledgeArticles.update', $data, ['article' => $record->id]);
    }
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()->using(function ($record) {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.support.knowledgeArticles.delete', parameters: ['article' => $record->id]);
        return true;
    })]; }
}
