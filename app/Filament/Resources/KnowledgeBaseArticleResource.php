<?php
namespace App\Filament\Resources;

use App\Filament\Resources\KnowledgeBaseArticleResource\Pages;
use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KnowledgeBaseArticleResource extends Resource
{
    protected static ?string $model = KnowledgeBaseArticle::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Knowledge Base';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status','draft')->count();
        return $count > 0 ? (string) $count : null;
    }
    public static function getNavigationBadgeColor(): ?string { return 'warning'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Article')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()->maxLength(250)->columnSpanFull()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->maxLength(250)->unique(ignoreRecord: true)->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Classification')->columns(2)->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(fn () => KnowledgeBaseCategory::activeOptions())
                    ->searchable()->nullable(),
                Forms\Components\Select::make('author_id')
                    ->label('Author')
                    ->options(fn () => User::orderBy('name')->pluck('name','id'))
                    ->default(fn () => auth()->id())->searchable()->required(),
                Forms\Components\Select::make('status')
                    ->options(KnowledgeBaseArticle::statusOptions())->default('draft')->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state === 'published') {
                            $set('published_at', now()->toDateTimeString());
                        }
                    }),
                Forms\Components\Toggle::make('is_public')->label('Public (Customer Portal)')->default(true),
                Forms\Components\DateTimePicker::make('published_at')->label('Published At')->nullable(),
                Forms\Components\TagsInput::make('tags')->label('Tags')->nullable(),
            ]),
            Forms\Components\Section::make('Content')->schema([
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->rows(20)
                    ->helperText('Supports basic HTML or plain text')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->weight('semibold')->limit(50)->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->placeholder('Uncategorized'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($s) => match($s) {
                        'published'=>'success','draft'=>'warning','archived'=>'gray', default=>'gray',
                    }),
                Tables\Columns\IconColumn::make('is_public')->label('Public')->boolean(),
                Tables\Columns\TextColumn::make('views')->sortable()->alignCenter(),
                Tables\Columns\TextColumn::make('author.name')->label('Author')->toggleable(),
                Tables\Columns\TextColumn::make('published_at')->label('Published')->date('d M Y')->placeholder('—')->sortable(),
            ])
            ->defaultSort('created_at','desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(KnowledgeBaseArticle::statusOptions()),
                Tables\Filters\SelectFilter::make('category_id')->label('Category')->relationship('category','name'),
                Tables\Filters\TernaryFilter::make('is_public')->label('Public'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label('Publish')->icon('heroicon-o-check-circle')->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (KnowledgeBaseArticle $r) => $r->status === 'published')
                    ->action(fn (KnowledgeBaseArticle $r) => $r->update(['status'=>'published','published_at'=>$r->published_at ?? now()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Article')->columns(3)->schema([
                Infolists\Components\TextEntry::make('title')->columnSpan(2)->weight('semibold'),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($s) => match($s) {
                        'published'=>'success','draft'=>'warning','archived'=>'gray', default=>'gray',
                    }),
                Infolists\Components\TextEntry::make('category.name')->label('Category')->placeholder('Uncategorized'),
                Infolists\Components\TextEntry::make('author.name')->label('Author'),
                Infolists\Components\TextEntry::make('views'),
                Infolists\Components\IconEntry::make('is_public')->label('Public')->boolean(),
                Infolists\Components\TextEntry::make('published_at')->dateTime('d M Y H:i')->placeholder('—'),
                Infolists\Components\TextEntry::make('tags')
                    ->formatStateUsing(fn ($s) => is_array($s) ? implode(', ', $s) : '—')->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Content')->schema([
                Infolists\Components\TextEntry::make('content')->html()->columnSpanFull()
                    ->formatStateUsing(fn ($s) => nl2br(e($s))),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array { return ['title','slug']; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKnowledgeBaseArticles::route('/'),
            'create' => Pages\CreateKnowledgeBaseArticle::route('/create'),
            'view'   => Pages\ViewKnowledgeBaseArticle::route('/{record}'),
            'edit'   => Pages\EditKnowledgeBaseArticle::route('/{record}/edit'),
        ];
    }
}
