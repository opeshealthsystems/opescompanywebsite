<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.publishing.blogPosts.update', $data, ['blogPost' => $record->id]);
    }

    protected function handleRecordDeletion(\Illuminate\Database\Eloquent\Model $record): void
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.publishing.blogPosts.delete', parameters: ['blogPost' => $record->id]);
    }
}
