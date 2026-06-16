<?php
namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Models\Project;
use App\Models\Timesheet;
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
            Forms\Components\DatePicker::make('date')->required()->default(now())->native(false),
            Forms\Components\Select::make('project_id')
                ->label('Project')
                ->options(fn () => Project::whereIn('status',['active','planning'])->orderBy('title')->pluck('title','id'))
                ->searchable()->nullable()->placeholder('No project'),
            Forms\Components\TextInput::make('hours')->numeric()->required()->minValue(0.25)->step(0.25)->suffix('hrs'),
            Forms\Components\Toggle::make('is_billable')->label('Billable')->default(false),
            Forms\Components\Textarea::make('description')->rows(2)->nullable()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('project.title')->label('Project')->placeholder('—')->limit(25),
                Tables\Columns\TextColumn::make('hours')->suffix(' hrs'),
                Tables\Columns\IconColumn::make('is_billable')->label('Billable')->boolean(),
                Tables\Columns\TextColumn::make('description')->limit(35)->placeholder('—'),
            ])
            ->defaultSort('date','desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
