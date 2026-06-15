<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TesterAssignmentResource\Pages;
use App\Models\TesterAssignment;
use App\Models\User;
use App\Support\ProductCatalog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TesterAssignmentResource extends Resource
{
    protected static ?string $model = TesterAssignment::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 35;
    protected static ?string $label = 'Tester Assignment';
    protected static ?string $pluralLabel = 'Tester Assignments';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('assign_testers') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return static::canAccess();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return static::canAccess();
    }

    public static function getProductOptions(): array
    {
        return ProductCatalog::options();
    }

    public static function form(Form $form): Form
    {
        $productOptions = static::getProductOptions();

        return $form->schema([
            Forms\Components\Section::make('Assignment Details')->schema([
                Forms\Components\Select::make('assigned_to')
                    ->label('Tester')
                    ->options(fn () => User::role('tester')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('product_slug')
                    ->label('Product')
                    ->options($productOptions)
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) use ($productOptions) {
                        if ($state && isset($productOptions[$state])) {
                            $set('product_name', $productOptions[$state]);
                        }
                    }),

                Forms\Components\Hidden::make('product_name'),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->options(TesterAssignment::statusOptions())
                    ->default('pending')
                    ->required(),

                Forms\Components\DatePicker::make('due_date')
                    ->nullable(),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tester.name')
                    ->label('Tester')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'     => 'gray',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Assigned')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TesterAssignment::statusOptions()),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Tester')
                    ->options(fn () => User::role('tester')->orderBy('name')->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTesterAssignments::route('/'),
            'create' => Pages\CreateTesterAssignment::route('/create'),
            'view'   => Pages\ViewTesterAssignment::route('/{record}'),
            'edit'   => Pages\EditTesterAssignment::route('/{record}/edit'),
        ];
    }
}
