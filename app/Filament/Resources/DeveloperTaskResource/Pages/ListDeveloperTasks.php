<?php

namespace App\Filament\Resources\DeveloperTaskResource\Pages;

use App\Filament\Resources\DeveloperTaskResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperTasks extends ListRecords
{
    protected static string $resource = DeveloperTaskResource::class;

    public function getTabs(): array
    {
        return [
            'all'         => Tab::make('All'),
            'open'        => Tab::make('Open')->modifyQueryUsing(fn ($q) => $q->where('status', 'open')),
            'in_progress' => Tab::make('In Progress')->modifyQueryUsing(fn ($q) => $q->where('status', 'in_progress')),
            'fixed'       => Tab::make('Fixed')->modifyQueryUsing(fn ($q) => $q->where('status', 'fixed')),
            'reopened'    => Tab::make('Reopened')->modifyQueryUsing(fn ($q) => $q->where('status', 'reopened')),
            'wont_fix'    => Tab::make("Won't Fix")->modifyQueryUsing(fn ($q) => $q->where('status', 'wont_fix')),
        ];
    }
}
