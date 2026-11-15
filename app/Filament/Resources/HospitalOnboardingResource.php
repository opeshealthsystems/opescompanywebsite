<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HospitalOnboardingResource\Pages;
use App\Models\HospitalOnboarding;
use App\Models\OnboardingStaff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HospitalOnboardingResource extends Resource
{
    protected static ?string $model = HospitalOnboarding::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'EMR Onboarding';
    protected static ?string $modelLabel = 'EMR onboarding';
    protected static ?string $recordTitleAttribute = 'facility_name';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'new')->count() ?: null;
    }

    private static function facilityTypeOptions(): array
    {
        return collect(HospitalOnboarding::facilityTypes())->map(fn ($v) => $v['en'])->all();
    }

    private static function statusOptions(): array
    {
        return collect(HospitalOnboarding::$statusLabels)->map(fn ($v) => $v['en'])->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Facility')->columns(2)->schema([
                Forms\Components\TextInput::make('facility_name')->required()->maxLength(150)->columnSpanFull(),
                Forms\Components\Select::make('facility_type')->options(static::facilityTypeOptions())->searchable(),
                Forms\Components\Select::make('facility_size')->label('Size (staff)')
                    ->options(array_combine(HospitalOnboarding::sizes(), HospitalOnboarding::sizes())),
                Forms\Components\TextInput::make('country')->maxLength(60),
                Forms\Components\TextInput::make('city')->label('City / Region')->maxLength(80),
                Forms\Components\TextInput::make('address')->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(30),
                Forms\Components\TextInput::make('email')->email()->maxLength(150),
                Forms\Components\TextInput::make('website')->maxLength(150),
                Forms\Components\TextInput::make('registration_number')->label('Registration / licence no.')->maxLength(80),
            ]),

            Forms\Components\Section::make('Primary contact')->columns(2)->schema([
                Forms\Components\TextInput::make('contact_name')->required()->maxLength(120),
                Forms\Components\TextInput::make('contact_title')->label('Title / Role')->maxLength(80),
                Forms\Components\TextInput::make('contact_phone')->tel()->maxLength(30),
                Forms\Components\TextInput::make('contact_email')->email()->required()->maxLength(150),
            ]),

            Forms\Components\Section::make('Demo scope')->columns(2)->schema([
                Forms\Components\TagsInput::make('products')->label('Products / modules')->placeholder('Add product')->columnSpanFull(),
                Forms\Components\TextInput::make('departments')->label('Departments / units in scope')->maxLength(500)->columnSpanFull(),
                Forms\Components\DatePicker::make('preferred_start_date'),
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Staff who will access the system')
                ->description('Each person who needs an EMR account. Tick "EMR account created" as you provision them on the EMR platform.')
                ->schema([
                    Forms\Components\Repeater::make('staff')
                        ->relationship()
                        ->label('')
                        ->columns(2)
                        ->defaultItems(0)
                        ->collapsible()
                        ->cloneable()
                        ->itemLabel(fn (array $state): ?string => $state['full_name'] ?? null)
                        ->addActionLabel('Add staff member')
                        ->schema([
                            Forms\Components\TextInput::make('full_name')->required()->maxLength(120),
                            Forms\Components\Select::make('job_role')
                                ->options(collect(OnboardingStaff::$jobRoles)->map(fn ($v) => $v['en'])->all()),
                            Forms\Components\TextInput::make('department')->maxLength(80),
                            Forms\Components\Select::make('access_level')
                                ->options(collect(OnboardingStaff::$accessLevels)->map(fn ($v) => $v['en'])->all()),
                            Forms\Components\TextInput::make('phone')->tel()->maxLength(30),
                            Forms\Components\TextInput::make('email')->email()->maxLength(150),
                            Forms\Components\Toggle::make('takes_tasks')
                                ->label('Takes tasks')
                                ->helperText('Reported by the facility'),
                            Forms\Components\Toggle::make('emr_account_created')
                                ->label('EMR account created')
                                ->helperText('Tick once provisioned in the EMR'),
                            Forms\Components\TextInput::make('emr_username')->label('EMR username')->maxLength(120),
                            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                        ]),
                ]),

            Forms\Components\Section::make('Admin')->columns(2)->schema([
                Forms\Components\Select::make('status')->options(static::statusOptions())->default('new')->required(),
                Forms\Components\TextInput::make('locale')->disabled()->dehydrated(false),
                Forms\Components\Textarea::make('admin_notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('facility_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_name')->searchable(),
                Tables\Columns\TextColumn::make('facility_type')
                    ->formatStateUsing(fn (?string $state) => $state ? (HospitalOnboarding::facilityTypes()[$state]['en'] ?? $state) : '—')
                    ->badge(),
                Tables\Columns\TextColumn::make('staff_count')->counts('staff')->label('Staff')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn (string $state) => HospitalOnboarding::$statusLabels[$state]['en'] ?? $state)
                    ->colors([
                        'warning' => 'new',
                        'primary' => 'reviewed',
                        'info'    => 'accounts_created',
                        'success' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(static::statusOptions()),
                Tables\Filters\SelectFilter::make('facility_type')->options(static::facilityTypeOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListHospitalOnboardings::route('/'),
            'create' => Pages\CreateHospitalOnboarding::route('/create'),
            'view'   => Pages\ViewHospitalOnboarding::route('/{record}'),
            'edit'   => Pages\EditHospitalOnboarding::route('/{record}/edit'),
        ];
    }
}
