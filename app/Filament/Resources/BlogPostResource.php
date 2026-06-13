<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Blog Posts';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('English Content')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) =>
                        $set('slug', Str::slug($state ?? ''))
                    ),
                Forms\Components\TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('excerpt')->maxLength(255),
                Forms\Components\RichEditor::make('body')->required()->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('French Translation (optional)')->schema([
                Forms\Components\TextInput::make('title_fr')->label('Title (FR)')->maxLength(255),
                Forms\Components\TextInput::make('excerpt_fr')->label('Excerpt (FR)')->maxLength(255),
                Forms\Components\RichEditor::make('body_fr')->label('Body (FR)')->columnSpanFull(),
            ])->columns(2)->collapsed(),

            Forms\Components\Section::make('Settings')->schema([
                Forms\Components\FileUpload::make('cover_image')->image()->directory('blog'),
                Forms\Components\TextInput::make('category')->required()->maxLength(100)->default('Digital Health'),
                Forms\Components\TextInput::make('author')->required()->maxLength(100)->default('OPES Health Systems'),
                Forms\Components\Toggle::make('published')
                    ->live()
                    ->afterStateUpdated(fn (Set $set, bool $state) =>
                        $state ? $set('published_at', now()) : null
                    ),
                Forms\Components\DateTimePicker::make('published_at')->label('Publish at'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50)->weight('bold'),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('author'),
                Tables\Columns\IconColumn::make('published')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->label('Published')->dateTime('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->dateTime('d M Y')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('published'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
