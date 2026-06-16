<?php
namespace App\Filament\Resources;

use App\Filament\Resources\TrainingRecordResource\Pages;
use App\Models\TrainingRecord;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingRecordResource extends Resource
{
    protected static ?string $model = TrainingRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Training Records';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $expiring = static::getModel()::where('status','completed')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at','<=', now()->addDays(30))
            ->whereDate('expires_at','>=', now())
            ->count();
        return $expiring > 0 ? (string) $expiring : null;
    }
    public static function getNavigationBadgeColor(): ?string { return 'warning'; }
    public static function getNavigationBadgeTooltip(): ?string { return 'Certifications expiring within 30 days'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Training Record')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),
                Forms\Components\Select::make('user_id')
                    ->label('Employee')
                    ->options(fn () => User::orderBy('name')->pluck('name','id'))
                    ->searchable()->required(),
                Forms\Components\TextInput::make('provider')->label('Training Provider')->nullable()->maxLength(150),
                Forms\Components\Select::make('category')->options(TrainingRecord::categoryOptions())->default('other')->required(),
                Forms\Components\Select::make('status')->options(TrainingRecord::statusOptions())->default('planned')->required(),
                Forms\Components\DatePicker::make('start_date')->nullable()->native(false),
                Forms\Components\DatePicker::make('completed_at')->label('Completed On')->nullable()->native(false),
                Forms\Components\DatePicker::make('expires_at')->label('Expires On')->nullable()->native(false),
                Forms\Components\FileUpload::make('certificate_path')->label('Certificate')->directory('certificates')->acceptedFileTypes(['application/pdf','image/*'])->nullable(),
                Forms\Components\Textarea::make('notes')->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->weight('semibold')->limit(40),
                Tables\Columns\TextColumn::make('employee.name')->label('Employee')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('category')->badge()->color('gray')
                    ->formatStateUsing(fn ($s) => TrainingRecord::categoryOptions()[$s] ?? $s),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($s) => match($s) {
                        'completed'=>'success','in_progress'=>'info','planned'=>'gray','expired'=>'danger', default=>'gray',
                    }),
                Tables\Columns\TextColumn::make('provider')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('completed_at')->label('Completed')->date('d M Y')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('expires_at')->label('Expires')->date('d M Y')->placeholder('—')->sortable()
                    ->color(fn (TrainingRecord $r) => $r->isExpiringSoon() ? 'warning' : ($r->expires_at?->isPast() ? 'danger' : null)),
            ])
            ->defaultSort('created_at','desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(TrainingRecord::statusOptions()),
                Tables\Filters\SelectFilter::make('category')->options(TrainingRecord::categoryOptions()),
                Tables\Filters\SelectFilter::make('user_id')->label('Employee')->relationship('employee','name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('complete')
                    ->label('Mark Complete')->icon('heroicon-o-check-circle')->color('success')
                    ->hidden(fn (TrainingRecord $r) => $r->status === 'completed')
                    ->action(fn (TrainingRecord $r) => $r->update(['status'=>'completed','completed_at'=>$r->completed_at ?? now()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Training Record')->columns(3)->schema([
                Infolists\Components\TextEntry::make('title')->columnSpan(2)->weight('semibold'),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($s) => match($s) {
                        'completed'=>'success','in_progress'=>'info','planned'=>'gray','expired'=>'danger', default=>'gray',
                    }),
                Infolists\Components\TextEntry::make('employee.name')->label('Employee'),
                Infolists\Components\TextEntry::make('provider')->label('Provider')->placeholder('—'),
                Infolists\Components\TextEntry::make('category')->badge()->color('gray')
                    ->formatStateUsing(fn ($s) => TrainingRecord::categoryOptions()[$s] ?? $s),
                Infolists\Components\TextEntry::make('start_date')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('completed_at')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('expires_at')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array { return ['title','provider']; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTrainingRecords::route('/'),
            'create' => Pages\CreateTrainingRecord::route('/create'),
            'view'   => Pages\ViewTrainingRecord::route('/{record}'),
            'edit'   => Pages\EditTrainingRecord::route('/{record}/edit'),
        ];
    }
}
