<?php
namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Timesheet;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TimesheetsRelationManager extends RelationManager
{
    protected static string $relationship = 'timesheets';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Staff Member')
                ->options(fn () => User::orderBy('name')->pluck('name','id'))
                ->searchable()->required(),
            Forms\Components\DatePicker::make('date')->required()->default(now())->native(false),
            Forms\Components\TextInput::make('hours')->numeric()->required()->minValue(0.25)->maxValue(24)->step(0.25)->suffix('hrs'),
            Forms\Components\Toggle::make('is_billable')->label('Billable')->default(false),
            Forms\Components\Textarea::make('description')->rows(2)->nullable()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Staff')->sortable(),
                Tables\Columns\TextColumn::make('date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('hours')->suffix(' hrs')->sortable(),
                Tables\Columns\IconColumn::make('is_billable')->label('Billable')->boolean(),
                Tables\Columns\TextColumn::make('description')->limit(40)->placeholder('—'),
            ])
            ->defaultSort('date','desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
