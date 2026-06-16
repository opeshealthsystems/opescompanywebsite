<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryGradeResource\Pages;
use App\Models\SalaryGrade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalaryGradeResource extends Resource
{
    protected static ?string $model = SalaryGrade::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Salary Grades';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 11;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Salary Grade')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(100),
                Forms\Components\TextInput::make('code')->required()->maxLength(20)->unique(ignoreRecord: true)->helperText('Short identifier, e.g. G1, SN, EX'),
                Forms\Components\TextInput::make('min_salary')->label('Min Salary')->numeric()->default(0)->minValue(0),
                Forms\Components\TextInput::make('max_salary')->label('Max Salary')->numeric()->default(0)->minValue(0),
                Forms\Components\Select::make('currency')->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])->default('XAF'),
                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
                Forms\Components\Textarea::make('description')->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->badge()->color('gray')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->weight('semibold'),
                Tables\Columns\TextColumn::make('salary_range')
                    ->label('Salary Band')
                    ->getStateUsing(fn (SalaryGrade $r) => $r->currency . ' ' . number_format((float) $r->min_salary, 0) . ' – ' . number_format((float) $r->max_salary, 0)),
                Tables\Columns\TextColumn::make('employees_count')->label('Employees')->counts('employees')->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->defaultSort('code')
            ->filters([Tables\Filters\TernaryFilter::make('is_active')->label('Active')])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (SalaryGrade $record, Tables\Actions\DeleteAction $action) {
                        if ($record->employees()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot delete: employees assigned to this grade')
                                ->danger()
                                ->send();
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
                Infolists\Components\TextEntry::make('code')->badge()->color('gray'),
                Infolists\Components\TextEntry::make('name')->weight('semibold'),
                Infolists\Components\IconEntry::make('is_active')->label('Active')->boolean(),
                Infolists\Components\TextEntry::make('min_salary')
                    ->getStateUsing(fn ($r) => $r->currency . ' ' . number_format((float) $r->min_salary, 0))
                    ->label('Min Salary'),
                Infolists\Components\TextEntry::make('max_salary')
                    ->getStateUsing(fn ($r) => $r->currency . ' ' . number_format((float) $r->max_salary, 0))
                    ->label('Max Salary'),
                Infolists\Components\TextEntry::make('description')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSalaryGrades::route('/'),
            'create' => Pages\CreateSalaryGrade::route('/create'),
            'view'   => Pages\ViewSalaryGrade::route('/{record}'),
            'edit'   => Pages\EditSalaryGrade::route('/{record}/edit'),
        ];
    }
}
