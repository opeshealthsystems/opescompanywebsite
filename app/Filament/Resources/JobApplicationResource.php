<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobApplicationResource\Pages;
use App\Models\JobApplication;
use App\Models\JobOpening;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;
    protected static ?string $navigationIcon  = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Applications';
    protected static ?string $navigationGroup = 'People';
    protected static ?int    $navigationSort  = 32;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Application Details')->columns(2)->schema([
                Forms\Components\Select::make('job_opening_id')
                    ->label('Job Opening')
                    ->options(JobOpening::where('status', 'open')->pluck('title', 'id'))
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('applicant_name')
                    ->required()
                    ->maxLength(200),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(150),

                Forms\Components\TextInput::make('phone')
                    ->nullable()
                    ->maxLength(50),

                Forms\Components\Select::make('status')
                    ->options(JobApplication::statusOptions())
                    ->default('received')
                    ->required(),

                Forms\Components\DatePicker::make('applied_at')
                    ->default(now())
                    ->native(false),

                Forms\Components\DatePicker::make('interview_date')
                    ->label('Interview Date')
                    ->nullable()
                    ->native(false),
            ]),

            Forms\Components\Section::make('Documents & Notes')->schema([
                Forms\Components\FileUpload::make('resume_path')
                    ->label('Resume / CV')
                    ->directory('resumes')
                    ->acceptedFileTypes(['application/pdf'])
                    ->nullable(),

                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant_name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('jobOpening.title')
                    ->label('Position')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'hired'       => 'success',
                        'offered'     => 'success',
                        'shortlisted' => 'info',
                        'interviewed' => 'info',
                        'rejected'    => 'danger',
                        'reviewing'   => 'warning',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => JobApplication::statusOptions()[$state] ?? ucfirst($state)),

                Tables\Columns\TextColumn::make('applied_at')
                    ->label('Applied')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('interview_date')
                    ->label('Interview')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('applied_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(JobApplication::statusOptions()),

                Tables\Filters\SelectFilter::make('job_opening_id')
                    ->label('Position')
                    ->options(JobOpening::pluck('title', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->hidden(fn (JobApplication $r) => in_array($r->status, ['hired', 'rejected'])),
                Tables\Actions\Action::make('shortlist')
                    ->label('Shortlist')
                    ->icon('heroicon-o-star')
                    ->color('info')
                    ->hidden(fn (JobApplication $r) => !in_array($r->status, ['received', 'reviewing']))
                    ->action(fn (JobApplication $r) => $r->update(['status' => 'shortlisted'])),
                Tables\Actions\Action::make('schedule_interview')
                    ->label('Schedule Interview')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->hidden(fn (JobApplication $r) => !in_array($r->status, ['shortlisted']))
                    ->form([
                        Forms\Components\DatePicker::make('interview_date')
                            ->label('Interview Date')
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (JobApplication $r, array $data) {
                        $r->update(['status' => 'interviewed', 'interview_date' => $data['interview_date']]);
                    }),
                Tables\Actions\Action::make('make_offer')
                    ->label('Make Offer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (JobApplication $r) => $r->status !== 'interviewed')
                    ->requiresConfirmation()
                    ->action(fn (JobApplication $r) => $r->update(['status' => 'offered'])),
                Tables\Actions\Action::make('hire')
                    ->label('Hire')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (JobApplication $r) => $r->status !== 'offered')
                    ->action(fn (JobApplication $r) => $r->update(['status' => 'hired'])),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn (JobApplication $r) => in_array($r->status, ['hired', 'rejected']))
                    ->action(fn (JobApplication $r) => $r->update(['status' => 'rejected'])),
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
            'index'  => Pages\ListJobApplications::route('/'),
            'create' => Pages\CreateJobApplication::route('/create'),
            'view'   => Pages\ViewJobApplication::route('/{record}'),
            'edit'   => Pages\EditJobApplication::route('/{record}/edit'),
        ];
    }
}
