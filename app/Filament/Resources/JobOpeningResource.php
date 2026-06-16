<?php
namespace App\Filament\Resources;

use App\Filament\Resources\JobOpeningResource\Pages;
use App\Filament\Resources\JobOpeningResource\RelationManagers\ApplicationsRelationManager;
use App\Models\Department;
use App\Models\JobOpening;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobOpeningResource extends Resource
{
    protected static ?string $model = JobOpening::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Job Openings';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 9;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status','open')->count();
        return $count > 0 ? (string) $count : null;
    }
    public static function getNavigationBadgeColor(): ?string { return 'success'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Job Opening')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),
                Forms\Components\Select::make('department_id')
                    ->label('Department')
                    ->options(fn () => Department::where('is_active',true)->orderBy('name')->pluck('name','id'))
                    ->searchable()->nullable(),
                Forms\Components\Select::make('type')->options(JobOpening::typeOptions())->default('full_time')->required(),
                Forms\Components\TextInput::make('location')->nullable()->maxLength(150),
                Forms\Components\Select::make('status')->options(JobOpening::statusOptions())->default('open')->required(),
                Forms\Components\TextInput::make('openings_count')->label('# Openings')->numeric()->default(1)->minValue(1),
                Forms\Components\TextInput::make('salary_range')->label('Salary Range')->nullable()->placeholder('e.g. XAF 200,000 – 350,000/month'),
                Forms\Components\Select::make('created_by')
                    ->label('Posted By')
                    ->options(fn () => User::orderBy('name')->pluck('name','id'))
                    ->default(fn () => auth()->id())->searchable()->required(),
            ]),
            Forms\Components\Section::make('Dates')->columns(2)->schema([
                Forms\Components\DatePicker::make('posted_at')->default(now())->native(false),
                Forms\Components\DatePicker::make('closes_at')->label('Closes At')->nullable()->native(false),
            ]),
            Forms\Components\Section::make('Description')->schema([
                Forms\Components\Textarea::make('description')->rows(5)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('requirements')->rows(5)->nullable()->columnSpanFull(),
            ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->weight('semibold')->limit(40),
                Tables\Columns\TextColumn::make('department.name')->label('Dept')->placeholder('—'),
                Tables\Columns\TextColumn::make('type')->badge()->color('gray')
                    ->formatStateUsing(fn ($s) => JobOpening::typeOptions()[$s] ?? $s),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($s) => match($s) {
                        'open'=>'success','paused'=>'warning','closed'=>'gray','filled'=>'info', default=>'gray',
                    }),
                Tables\Columns\TextColumn::make('openings_count')->label('#')->alignCenter(),
                Tables\Columns\TextColumn::make('applications_count')->label('Applied')
                    ->counts('applications')->alignCenter(),
                Tables\Columns\TextColumn::make('closes_at')->label('Closes')->date('d M Y')->placeholder('—')->sortable(),
            ])
            ->defaultSort('created_at','desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(JobOpening::statusOptions()),
                Tables\Filters\SelectFilter::make('type')->options(JobOpening::typeOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('close')
                    ->label('Close')->icon('heroicon-o-x-circle')->color('gray')
                    ->requiresConfirmation()
                    ->hidden(fn (JobOpening $r) => !in_array($r->status, ['open','paused']))
                    ->action(fn (JobOpening $r) => $r->update(['status'=>'closed'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Job Opening')->columns(3)->schema([
                Infolists\Components\TextEntry::make('title')->columnSpan(2)->weight('semibold'),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($s) => match($s) {
                        'open'=>'success','paused'=>'warning','closed'=>'gray','filled'=>'info', default=>'gray',
                    }),
                Infolists\Components\TextEntry::make('department.name')->label('Department')->placeholder('—'),
                Infolists\Components\TextEntry::make('type')->badge()->color('gray')
                    ->formatStateUsing(fn ($s) => JobOpening::typeOptions()[$s] ?? $s),
                Infolists\Components\TextEntry::make('location')->placeholder('—'),
                Infolists\Components\TextEntry::make('openings_count')->label('# Openings'),
                Infolists\Components\TextEntry::make('salary_range')->label('Salary Range')->placeholder('—'),
                Infolists\Components\TextEntry::make('closes_at')->date('d M Y')->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Description')->collapsible()->schema([
                Infolists\Components\TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                Infolists\Components\TextEntry::make('requirements')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getRelations(): array { return [ApplicationsRelationManager::class]; }

    public static function getGloballySearchableAttributes(): array { return ['title','location']; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJobOpenings::route('/'),
            'create' => Pages\CreateJobOpening::route('/create'),
            'view'   => Pages\ViewJobOpening::route('/{record}'),
            'edit'   => Pages\EditJobOpening::route('/{record}/edit'),
        ];
    }
}
