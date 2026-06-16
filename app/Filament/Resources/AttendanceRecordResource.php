<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceRecordResource\Pages;
use App\Models\AttendanceRecord;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceRecordResource extends Resource
{
    protected static ?string $model = AttendanceRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Attendance';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 12;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $absentToday = static::getModel()::whereDate('date', today())->where('status','absent')->count();
        return $absentToday > 0 ? (string) $absentToday : null;
    }
    public static function getNavigationBadgeColor(): ?string { return 'danger'; }
    public static function getNavigationBadgeTooltip(): ?string { return 'Absent today'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Attendance')->columns(2)->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Employee')
                    ->options(fn () => User::orderBy('name')->pluck('name','id'))
                    ->searchable()->required(),
                Forms\Components\DatePicker::make('date')->required()->default(today())->native(false),
                Forms\Components\Select::make('status')
                    ->options(AttendanceRecord::statusOptions())->default('present')->required(),
                Forms\Components\TimePicker::make('check_in')->label('Check In')->nullable()->seconds(false),
                Forms\Components\TimePicker::make('check_out')->label('Check Out')->nullable()->seconds(false),
                Forms\Components\Textarea::make('notes')->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('employee.name')->label('Employee')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'present'  => 'success',
                        'remote'   => 'info',
                        'late'     => 'warning',
                        'half_day' => 'warning',
                        'absent'   => 'danger',
                        'on_leave' => 'gray',
                        'holiday'  => 'gray',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($s) => AttendanceRecord::statusOptions()[$s] ?? $s),
                Tables\Columns\TextColumn::make('check_in')->label('In')->placeholder('—'),
                Tables\Columns\TextColumn::make('check_out')->label('Out')->placeholder('—'),
                Tables\Columns\TextColumn::make('formatted_hours')->label('Hours')->getStateUsing(fn (AttendanceRecord $r) => $r->formatted_hours),
                Tables\Columns\TextColumn::make('notes')->limit(30)->placeholder('—')->toggleable(),
            ])
            ->defaultSort('date','desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(AttendanceRecord::statusOptions()),
                Tables\Filters\SelectFilter::make('user_id')->label('Employee')->relationship('employee','name'),
                Tables\Filters\Filter::make('today')
                    ->label('Today Only')
                    ->query(fn ($query) => $query->whereDate('date', today()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make()->columns(3)->schema([
                Infolists\Components\TextEntry::make('date')->date('d M Y')->weight('semibold'),
                Infolists\Components\TextEntry::make('employee.name')->label('Employee'),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($s) => match($s) {
                        'present'=>'success','remote'=>'info','late'=>'warning',
                        'half_day'=>'warning','absent'=>'danger', default=>'gray',
                    })
                    ->formatStateUsing(fn ($s) => AttendanceRecord::statusOptions()[$s] ?? $s),
                Infolists\Components\TextEntry::make('check_in')->label('Check In')->placeholder('—'),
                Infolists\Components\TextEntry::make('check_out')->label('Check Out')->placeholder('—'),
                Infolists\Components\TextEntry::make('formatted_hours')->label('Hours Worked')->getStateUsing(fn ($r) => $r->formatted_hours),
                Infolists\Components\TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAttendanceRecords::route('/'),
            'create' => Pages\CreateAttendanceRecord::route('/create'),
            'view'   => Pages\ViewAttendanceRecord::route('/{record}'),
            'edit'   => Pages\EditAttendanceRecord::route('/{record}/edit'),
        ];
    }
}
