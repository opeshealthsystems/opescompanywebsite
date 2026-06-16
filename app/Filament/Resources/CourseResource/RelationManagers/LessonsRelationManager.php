<?php
namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';
    protected static ?string $title = 'Lessons';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('sort_order')->numeric()->default(0),
            TextInput::make('title')->required()->maxLength(200),
            TextInput::make('title_fr')->label('Title (French)')->maxLength(200),
            TextInput::make('video_url')->url()->maxLength(500),
            TextInput::make('duration_minutes')->numeric()->label('Duration (min)'),
            Textarea::make('content')->rows(6)->columnSpanFull(),
            Textarea::make('content_fr')->label('Content (French)')->rows(6)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable(),
                TextColumn::make('title')->searchable(),
                IconColumn::make('video_url')->label('Video')->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->video_url)),
                TextColumn::make('duration_minutes')->label('Min')->suffix(' min'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
