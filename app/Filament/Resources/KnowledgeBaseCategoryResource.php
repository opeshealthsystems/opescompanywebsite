<?php
namespace App\Filament\Resources;

use App\Filament\Resources\KnowledgeBaseCategoryResource\Pages;
use App\Models\KnowledgeBaseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KnowledgeBaseCategoryResource extends Resource
{
    protected static ?string $model = KnowledgeBaseCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'KB Categories';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(150)->columnSpanFull()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->maxLength(150)->unique(ignoreRecord: true),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Category')
                    ->options(fn () => KnowledgeBaseCategory::where('is_active',true)->orderBy('name')->pluck('name','id'))
                    ->searchable()->nullable()->placeholder('Top-level'),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0)->label('Sort Order'),
                Forms\Components\Toggle::make('is_public')->label('Public (Customer Portal)')->default(true),
                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
                Forms\Components\Textarea::make('description')->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->weight('semibold')->sortable(),
                Tables\Columns\TextColumn::make('slug')->fontFamily('mono')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('parent.name')->label('Parent')->placeholder('Top-level'),
                Tables\Columns\TextColumn::make('articles_count')->label('Articles')->counts('articles')->alignCenter(),
                Tables\Columns\IconColumn::make('is_public')->label('Public')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (KnowledgeBaseCategory $record, Tables\Actions\DeleteAction $action) {
                        if ($record->articles()->exists() || $record->children()->exists()) {
                            \Filament\Notifications\Notification::make()->title('Cannot delete: has articles or sub-categories')->danger()->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make()->columns(3)->schema([
                Infolists\Components\TextEntry::make('name')->weight('semibold'),
                Infolists\Components\TextEntry::make('slug')->fontFamily('mono')->badge()->color('gray'),
                Infolists\Components\TextEntry::make('parent.name')->label('Parent')->placeholder('Top-level'),
                Infolists\Components\IconEntry::make('is_public')->label('Public')->boolean(),
                Infolists\Components\IconEntry::make('is_active')->label('Active')->boolean(),
                Infolists\Components\TextEntry::make('description')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKnowledgeBaseCategories::route('/'),
            'create' => Pages\CreateKnowledgeBaseCategory::route('/create'),
            'view'   => Pages\ViewKnowledgeBaseCategory::route('/{record}'),
            'edit'   => Pages\EditKnowledgeBaseCategory::route('/{record}/edit'),
        ];
    }
}
