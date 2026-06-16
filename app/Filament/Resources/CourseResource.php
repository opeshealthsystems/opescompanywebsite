<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers\LessonsRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\EnrollmentsRelationManager;
use App\Models\Course;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Course Details')->schema([
                TextInput::make('title')
                    ->required()->maxLength(200)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('title_fr')->label('Title (French)')->maxLength(200),
                TextInput::make('slug')->required()->maxLength(100)->unique(ignoreRecord: true),
                Select::make('level')->options(Course::levelOptions())->default('beginner')->required(),
                TextInput::make('duration_hours')->numeric()->label('Duration (hours)'),
                TextInput::make('product_slug')->maxLength(100)->label('Related Product Slug'),
                FileUpload::make('cover_image')->image()->directory('courses')->maxSize(2048),
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_active')->default(true),
                Toggle::make('is_featured'),
            ])->columns(2),
            Section::make('Content')->schema([
                Textarea::make('description')->rows(4),
                Textarea::make('description_fr')->label('Description (French)')->rows(4),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')->size(48),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('level')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Course::levelOptions()[$state] ?? $state),
                TextColumn::make('duration_hours')->label('Hours')->suffix('h'),
                TextColumn::make('enrollments_count')->counts('enrollments')->label('Enrolled'),
                ToggleColumn::make('is_active'),
                TextColumn::make('sort_order')->label('Order')->sortable(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LessonsRelationManager::class,
            EnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit'   => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
