<?php

namespace App\Filament\Resources\TestimonialResource\Pages;

use App\Filament\Resources\TestimonialResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTestimonial extends ViewRecord
{
    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make(), Actions\DeleteAction::make()->using(function ($record) {
            \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.communications.testimonials.delete', parameters: ['testimonial' => $record->id]);
            return true;
        })];
    }
}
