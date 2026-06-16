<?php
namespace App\Filament\Resources\JobOpeningResource\RelationManagers;

use App\Models\JobApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('applicant_name')->required()->maxLength(200),
            Forms\Components\TextInput::make('email')->email()->required()->maxLength(150),
            Forms\Components\TextInput::make('phone')->nullable()->maxLength(50),
            Forms\Components\Select::make('status')->options(JobApplication::statusOptions())->default('received')->required(),
            Forms\Components\DatePicker::make('applied_at')->default(now())->native(false),
            Forms\Components\DatePicker::make('interview_date')->label('Interview Date')->nullable()->native(false),
            Forms\Components\FileUpload::make('resume_path')->label('Resume/CV')->directory('resumes')->acceptedFileTypes(['application/pdf'])->nullable(),
            Forms\Components\Textarea::make('notes')->rows(2)->nullable()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant_name')->searchable()->weight('semibold'),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'hired'=>'success','offered'=>'success','shortlisted'=>'info',
                        'interviewed'=>'info','rejected'=>'danger','received'=>'gray', default=>'gray',
                    }),
                Tables\Columns\TextColumn::make('applied_at')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('interview_date')->label('Interview')->date('d M Y')->placeholder('—'),
            ])
            ->defaultSort('applied_at','desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('shortlist')
                    ->label('Shortlist')->icon('heroicon-o-star')->color('info')
                    ->hidden(fn (JobApplication $r) => !in_array($r->status, ['received','reviewing']))
                    ->action(fn (JobApplication $r) => $r->update(['status'=>'shortlisted'])),
                Tables\Actions\Action::make('hire')
                    ->label('Hire')->icon('heroicon-o-check-badge')->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (JobApplication $r) => $r->status !== 'offered')
                    ->action(fn (JobApplication $r) => $r->update(['status'=>'hired'])),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')->icon('heroicon-o-x-mark')->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn (JobApplication $r) => in_array($r->status, ['hired','rejected']))
                    ->action(fn (JobApplication $r) => $r->update(['status'=>'rejected'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
