<?php
namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Models\CourseCertificate;
use App\Models\CourseEnrollment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';
    protected static ?string $title = 'Enrollments';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Student')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'enrolled'    => 'gray',
                        'in_progress' => 'info',
                        'completed'   => 'success',
                        'dropped'     => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => CourseEnrollment::statusOptions()[$state] ?? $state),
                TextColumn::make('enrolled_at')->dateTime(),
                TextColumn::make('completed_at')->dateTime()->default('—'),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_completed')
                    ->label('Mark Completed')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (CourseEnrollment $record) => $record->status !== 'completed')
                    ->requiresConfirmation()
                    ->action(function (CourseEnrollment $record) {
                        $record->update(['status' => 'completed', 'completed_at' => now()]);

                        $cert = $record->certificate;
                        if (!$cert) {
                            $cert = CourseCertificate::create([
                                'enrollment_id' => $record->id,
                                'user_id'       => $record->user_id,
                                'course_id'     => $record->course_id,
                            ]);
                        }

                        if (class_exists(\App\Mail\CourseCertificateIssued::class)) {
                            \Illuminate\Support\Facades\Mail::to($record->user->email)
                                ->queue(new \App\Mail\CourseCertificateIssued($cert));
                        }

                        Notification::make()->title('Enrollment completed, certificate issued.')->success()->send();
                    }),
            ]);
    }
}
