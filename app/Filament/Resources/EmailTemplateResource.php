<?php
namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';
    protected static ?string $navigationLabel = 'Email Templates';
    protected static ?string $navigationGroup = 'Communications';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Template')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(150)->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->maxLength(100)->unique(ignoreRecord: true)
                    ->helperText('Used in code to retrieve this template, e.g. welcome, invoice-sent'),
                Forms\Components\Select::make('type')->options(EmailTemplate::typeOptions())->required(),
                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
            ]),
            Forms\Components\Section::make('Content')->schema([
                Forms\Components\TextInput::make('subject')->required()->maxLength(250)->columnSpanFull(),
                Forms\Components\Textarea::make('body')
                    ->required()
                    ->rows(12)
                    ->helperText('Use {{variable_name}} for dynamic values. Available variables are listed below.')
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('variables')
                    ->label('Available Variables')
                    ->helperText('List the variable names available for this template (without {{ }})')
                    ->nullable()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->weight('semibold')->sortable(),
                Tables\Columns\TextColumn::make('slug')->fontFamily('mono')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('type')->badge()
                    ->formatStateUsing(fn ($s) => EmailTemplate::typeOptions()[$s] ?? $s)
                    ->color('info'),
                Tables\Columns\TextColumn::make('subject')->limit(50)->searchable(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Last Updated')->since()->sortable(),
            ])
            ->defaultSort('type')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options(EmailTemplate::typeOptions()),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Template')->columns(3)->schema([
                Infolists\Components\TextEntry::make('name')->weight('semibold'),
                Infolists\Components\TextEntry::make('slug')->fontFamily('mono')->badge()->color('gray'),
                Infolists\Components\TextEntry::make('type')->badge()->color('info')
                    ->formatStateUsing(fn ($s) => EmailTemplate::typeOptions()[$s] ?? $s),
                Infolists\Components\IconEntry::make('is_active')->label('Active')->boolean(),
                Infolists\Components\TextEntry::make('updated_at')->label('Last Updated')->since(),
            ]),
            Infolists\Components\Section::make('Content')->schema([
                Infolists\Components\TextEntry::make('subject')->weight('semibold')->columnSpanFull(),
                Infolists\Components\TextEntry::make('body')
                    ->html()
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($s) => nl2br(e($s))),
            ]),
            Infolists\Components\Section::make('Variables')->collapsible()->schema([
                Infolists\Components\TextEntry::make('variables')
                    ->label('Available Variables')
                    ->formatStateUsing(fn ($s) => is_array($s) ? implode(', ', array_map(fn($v)=>'{{' . $v . '}}', $s)) : '—')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array { return ['name','slug','subject']; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'view'   => Pages\ViewEmailTemplate::route('/{record}'),
            'edit'   => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
