<?php
namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Models\PerformanceReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PerformanceReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'performanceReviews';
    protected static ?string $title = 'Performance Reviews';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('review_period')->required()->maxLength(50)->placeholder('e.g. Q1 2026'),
            Forms\Components\DatePicker::make('review_date')->required()->default(now())->native(false),
            Forms\Components\Select::make('overall_rating')
                ->options(PerformanceReview::ratingOptions())->default(3)->required(),
            Forms\Components\Select::make('status')
                ->options(PerformanceReview::statusOptions())->default('draft')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('review_period')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('review_date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('overall_rating')->label('Rating')
                    ->badge()
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state == 3 ? 'info' : 'warning'))
                    ->formatStateUsing(fn ($state) => $state.'/5'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'acknowledged'=>'success','submitted'=>'info','draft'=>'warning', default=>'gray',
                    }),
                Tables\Columns\TextColumn::make('reviewer.name')->label('Reviewer'),
            ])
            ->defaultSort('review_date','desc')
            ->headerActions([
                Tables\Actions\Action::make('new_review')
                    ->label('New Review')
                    ->url(fn () => \App\Filament\Resources\PerformanceReviewResource::getUrl('create', ['user_id' => $this->getOwnerRecord()->id]))
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (PerformanceReview $record) => \App\Filament\Resources\PerformanceReviewResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
