<?php
namespace App\Filament\Resources\KnowledgeBaseCategoryResource\Pages;
use App\Filament\Resources\KnowledgeBaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewKnowledgeBaseCategory extends ViewRecord {
    protected static string $resource = KnowledgeBaseCategoryResource::class;
    protected function getHeaderActions(): array { return [Actions\EditAction::make(), Actions\DeleteAction::make()]; }
}
