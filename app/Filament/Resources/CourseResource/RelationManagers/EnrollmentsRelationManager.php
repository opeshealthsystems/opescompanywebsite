<?php
namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Models\CourseEnrollment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';
    protected static ?string $title = 'Enrollments';

    protected function getTableQuery(): Builder
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::query(
            \App\Contexts\Learning\Application\Queries\AdminLearningRecordQueries::class,
            'enrollmentQuery',
        )->where('course_id', $this->getOwnerRecord()->getKey());
    }

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
                    ->action(fn (CourseEnrollment $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.learning.enrollments.complete', parameters: ['enrollment' => $record])),
            ]);
    }
}
