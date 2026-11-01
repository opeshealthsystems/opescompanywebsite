<?php

namespace App\Filament\Widgets;

use App\Contexts\Shared\Application\Queries\AdminDashboardReadQueries;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Models\Announcement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AnnouncementsWidget extends BaseWidget
{
    protected static ?string $heading = 'Staff Announcements';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 0;

    public function table(Table $table): Table
    {
        return $table
            ->query(app(AdminDashboardReadQueries::class)->announcementQuery(new WorkflowInput(actor: auth()->user(), language: app()->getLocale())))
            ->columns([
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('')->boolean()
                    ->trueIcon('heroicon-o-star')->trueColor('warning')
                    ->falseIcon('')->falseColor('gray')
                    ->width('24px'),

                Tables\Columns\TextColumn::make('title')
                    ->weight('semibold')
                    ->description(fn (Announcement $r) => strip_tags($r->body) ?
                        \Illuminate\Support\Str::limit(strip_tags($r->body), 100) : null),

                Tables\Columns\TextColumn::make('audience')
                    ->badge()->color('info')
                    ->formatStateUsing(fn ($state) => Announcement::audienceOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('author.name')->label('Posted By'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')->since()->sortable(),
            ])
            ->paginated(false);
    }
}
