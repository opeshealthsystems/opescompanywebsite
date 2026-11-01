<?php

namespace App\Filament\Resources\TestimonialResource\Pages;

use App\Filament\Resources\TestimonialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestimonial extends EditRecord
{
    protected static string $resource = TestimonialResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.communications.testimonials.update', $data, ['testimonial' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()->using(function ($record) {
            \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.communications.testimonials.delete', parameters: ['testimonial' => $record->id]);
            return true;
        })];
    }
}
