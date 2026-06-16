<?php
namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Models\TrainingRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainingRecords';
    protected static ?string $title = 'Training Records';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),
            Forms\Components\TextInput::make('provider')->maxLength(150)->nullable(),
            Forms\Components\Select::make('category')
                ->options(TrainingRecord::categoryOptions())->required(),
            Forms\Components\Select::make('status')
                ->options(TrainingRecord::statusOptions())->default('enrolled')->required(),
            Forms\Components\DatePicker::make('start_date')->nullable()->native(false),
            Forms\Components\DatePicker::make('completed_at')->label('Completed At')->nullable()->native(false),
            Forms\Components\DatePicker::make('expires_at')->label('Expires At')->nullable()->native(false),
            Forms\Components\Textarea::make('notes')->rows(2)->nullable()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->weight('semibold')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('category')->badge()->color('gray')
                    ->formatStateUsing(fn ($s) => TrainingRecord::categoryOptions()[$s] ?? $s),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($s) => match($s) {
                        'completed'=>'success','enrolled'=>'info','in_progress'=>'warning','expired'=>'danger', default=>'gray',
                    }),
                Tables\Columns\TextColumn::make('provider')->placeholder('—'),
                Tables\Columns\TextColumn::make('completed_at')->label('Completed')->date('d M Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('expires_at')->label('Expires')->date('d M Y')->placeholder('—')
                    ->color(fn (TrainingRecord $r) => $r->isExpiringSoon() ? 'warning' : null),
            ])
            ->defaultSort('start_date', 'desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (TrainingRecord $r) => $r->status === 'completed')
                    ->action(fn (TrainingRecord $r) => $r->update(['status'=>'completed','completed_at'=>$r->completed_at ?? now()])),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
