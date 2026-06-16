<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PractitionerProgramResource\Pages;
use App\Models\PractitionerProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PractitionerProgramResource extends Resource
{
    protected static ?string $model = PractitionerProgram::class;
    protected static ?string $navigationIcon  = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Programmes';
    protected static ?string $navigationGroup = 'Practitioners';
    protected static ?int    $navigationSort  = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Programme Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),
                    Forms\Components\TextInput::make('title_fr')->label('Title (French)')->maxLength(200)->nullable(),
                    Forms\Components\Select::make('type')
                        ->options(PractitionerProgram::typeOptions())
                        ->default('volunteer')
                        ->required()
                        ->live(),
                    Forms\Components\Select::make('status')
                        ->options(PractitionerProgram::statusOptions())
                        ->default('draft')
                        ->required(),
                    Forms\Components\TextInput::make('compensation')
                        ->placeholder('e.g. 50,000 XAF/month')
                        ->nullable()
                        ->visible(fn (Forms\Get $get) => $get('type') === 'paid'),
                    Forms\Components\TextInput::make('max_participants')
                        ->label('Max Participants')
                        ->numeric()
                        ->nullable(),
                    Forms\Components\TextInput::make('product_slug')->label('Product Slug')->nullable(),
                    Forms\Components\TextInput::make('product_name')->label('Product Name')->nullable(),
                    Forms\Components\DatePicker::make('starts_at')->nullable(),
                    Forms\Components\DatePicker::make('ends_at')->nullable(),
                ]),
            Forms\Components\Section::make('Description')
                ->schema([
                    Forms\Components\Textarea::make('description')->rows(4)->nullable(),
                    Forms\Components\Textarea::make('description_fr')->label('Description (French)')->rows(4)->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('type')->badge()
                    ->color(fn ($state) => $state === 'paid' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'open'     => 'success',
                        'closed'   => 'danger',
                        'draft'    => 'gray',
                        'archived' => 'warning',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Applications')
                    ->counts('applications'),
                Tables\Columns\TextColumn::make('starts_at')->date('d M Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('ends_at')->date('d M Y')->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(PractitionerProgram::statusOptions()),
                Tables\Filters\SelectFilter::make('type')->options(PractitionerProgram::typeOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Programme')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('title'),
                    Infolists\Components\TextEntry::make('type')->badge(),
                    Infolists\Components\TextEntry::make('status')->badge(),
                    Infolists\Components\TextEntry::make('compensation')->placeholder('—'),
                    Infolists\Components\TextEntry::make('max_participants')->label('Max Participants')->placeholder('Unlimited'),
                    Infolists\Components\TextEntry::make('starts_at')->date('d M Y')->placeholder('—'),
                    Infolists\Components\TextEntry::make('ends_at')->date('d M Y')->placeholder('—'),
                ]),
            Infolists\Components\Section::make('Description')->schema([
                Infolists\Components\TextEntry::make('description')->placeholder('—'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPractitionerPrograms::route('/'),
            'create' => Pages\CreatePractitionerProgram::route('/create'),
            'view'   => Pages\ViewPractitionerProgram::route('/{record}'),
            'edit'   => Pages\EditPractitionerProgram::route('/{record}/edit'),
        ];
    }
}
