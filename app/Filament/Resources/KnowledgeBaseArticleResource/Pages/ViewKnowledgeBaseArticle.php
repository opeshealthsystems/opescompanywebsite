<?php
namespace App\Filament\Resources\KnowledgeBaseArticleResource\Pages;
use App\Filament\Resources\KnowledgeBaseArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewKnowledgeBaseArticle extends ViewRecord {
    protected static string $resource = KnowledgeBaseArticleResource::class;
    protected function getHeaderActions(): array { return [Actions\EditAction::make(), Actions\Action::make('archive')->requiresConfirmation()->action(fn ($record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.support.knowledgeArticles.archive', parameters: ['article' => $record->id])), Actions\DeleteAction::make()->using(function ($record) {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.support.knowledgeArticles.delete', parameters: ['article' => $record->id]);
        return true;
    })]; }
}
