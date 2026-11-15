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

    private static function en(array $options): array
    {
        return collect($options)->map(fn ($v) => $v['en'])->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('A · Authorisation & consent')->columns(2)->collapsible()->schema([
                Forms\Components\TextInput::make('owner_name')->label('Owner / director')->maxLength(120),
                Forms\Components\TextInput::make('owner_title')->maxLength(80),
                Forms\Components\TextInput::make('owner_phone')->tel()->maxLength(30),
                Forms\Components\TextInput::make('owner_email')->email()->maxLength(150),
                Forms\Components\TextInput::make('contact_name')->label('Completed by')->required()->maxLength(120),
                Forms\Components\TextInput::make('completed_by_role')->label('Their role')->maxLength(80),
                Forms\Components\TextInput::make('contact_phone')->tel()->maxLength(30),
                Forms\Components\TextInput::make('contact_email')->email()->required()->maxLength(150),
                Forms\Components\Toggle::make('completer_authorised')->label('Authorised to approve accounts'),
                Forms\Components\Toggle::make('data_sharing_consent')->label('Data-sharing consent given'),
            ]),

            Forms\Components\Section::make('B · Clinic identity')->columns(2)->collapsible()->schema([
                Forms\Components\TextInput::make('facility_name')->label('Common name')->required()->maxLength(150),
                Forms\Components\Select::make('facility_type')->options(static::en(HospitalOnboarding::facilityTypes()))->searchable(),
                Forms\Components\TextInput::make('legal_name')->maxLength(160),
                Forms\Components\TextInput::make('trading_name')->maxLength(160),
                Forms\Components\TextInput::make('registration_number')->maxLength(80),
                Forms\Components\DatePicker::make('registration_date'),
                Forms\Components\TextInput::make('tax_number')->label('Tax no. (NIU)')->maxLength(60),
                Forms\Components\Select::make('working_language')->options(static::en(HospitalOnboarding::$workingLanguages)),
                Forms\Components\TextInput::make('licence_number')->label('Operating licence no.')->maxLength(80),
                Forms\Components\TextInput::make('licence_authority')->label('Issuing authority')->maxLength(120),
                Forms\Components\Select::make('facility_size')->label('Size (staff)')->options(array_combine(HospitalOnboarding::sizes(), HospitalOnboarding::sizes())),
                Forms\Components\TextInput::make('gps_location')->maxLength(120),
                Forms\Components\TextInput::make('country')->maxLength(60),
                Forms\Components\TextInput::make('city')->maxLength(80),
                Forms\Components\TextInput::make('address')->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(30),
                Forms\Components\TextInput::make('email')->label('Official email')->email()->maxLength(150),
                Forms\Components\TextInput::make('website')->maxLength(150),
                Forms\Components\TextInput::make('opening_hours')->maxLength(160),
                Forms\Components\Repeater::make('sites')->relationship()->label('Sites')->columns(2)->defaultItems(0)->collapsed()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                    ->addActionLabel('Add site')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(160),
                        Forms\Components\TextInput::make('address')->maxLength(255),
                    ])->columnSpanFull(),
            ]),

            Forms\Components\Section::make('C · Services')->columns(3)->collapsible()->schema([
                Forms\Components\CheckboxList::make('services')->options(static::en(HospitalOnboarding::$services))->columns(2)->columnSpanFull(),
                Forms\Components\TextInput::make('departments')->maxLength(500)->columnSpanFull(),
                Forms\Components\TextInput::make('beds')->numeric()->minValue(0),
                Forms\Components\TextInput::make('consulting_rooms')->numeric()->minValue(0),
                Forms\Components\TextInput::make('patients_per_day')->numeric()->minValue(0),
                Forms\Components\TextInput::make('insurers')->maxLength(1000)->columnSpan(2),
                Forms\Components\TextInput::make('mobile_money')->maxLength(255),
                Forms\Components\TextInput::make('vat_rate')->maxLength(20),
                Forms\Components\TextInput::make('currency')->maxLength(10),
            ]),

            Forms\Components\Section::make('D · Setup')->columns(2)->collapsible()->schema([
                Forms\Components\TagsInput::make('products')->label('Products / modules')->columnSpanFull(),
                Forms\Components\TextInput::make('devices')->maxLength(1000),
                Forms\Components\TextInput::make('connectivity_notes')->label('Internet / electricity')->maxLength(1000),
                Forms\Components\Select::make('email_domain_choice')->options(static::en(HospitalOnboarding::$emailDomainChoices)),
                Forms\Components\TextInput::make('email_domain')->maxLength(120),
                Forms\Components\TextInput::make('current_software')->maxLength(1000),
                Forms\Components\DatePicker::make('preferred_start_date'),
                Forms\Components\TextInput::make('training_attendees')->maxLength(1000)->columnSpanFull(),
            ]),

            Forms\Components\Section::make('E · Staff who will access the system')
                ->description('Tick "EMR account created" as you provision each person on the EMR platform.')
                ->collapsible()
                ->schema([
                    Forms\Components\Repeater::make('staff')->relationship()->label('')->columns(2)->defaultItems(0)->collapsible()->cloneable()
                        ->itemLabel(fn (array $state): ?string => $state['full_name'] ?? null)
                        ->addActionLabel('Add staff member')
                        ->schema([
                            Forms\Components\TextInput::make('full_name')->required()->maxLength(120),
                            Forms\Components\Select::make('job_role')->options(static::en(OnboardingStaff::$jobRoles)),
                            Forms\Components\TextInput::make('specialty')->maxLength(120),
                            Forms\Components\Select::make('access_level')->options(static::en(OnboardingStaff::$accessLevels)),
                            Forms\Components\TextInput::make('licence_number')->maxLength(80),
                            Forms\Components\TextInput::make('licence_issuing_body')->maxLength(120),
                            Forms\Components\DatePicker::make('licence_expiry'),
                            Forms\Components\TextInput::make('years_experience')->numeric()->minValue(0),
                            Forms\Components\TextInput::make('site')->maxLength(160),
                            Forms\Components\TextInput::make('department')->maxLength(80),
                            Forms\Components\TextInput::make('phone')->tel()->maxLength(30),
                            Forms\Components\TextInput::make('email')->email()->maxLength(150),
                            Forms\Components\TextInput::make('working_hours')->maxLength(160),
                            Forms\Components\TextInput::make('consultation_fee')->maxLength(40),
                            Forms\Components\FileUpload::make('signature_path')->label('Signature')->image()->disk('local')->directory('onboarding/signatures'),
                            Forms\Components\Toggle::make('takes_tasks')->label('Takes tasks')->helperText('Reported by the facility'),
                            Forms\Components\Toggle::make('emr_account_created')->label('EMR account created')->helperText('Tick once provisioned'),
                            Forms\Components\TextInput::make('emr_username')->maxLength(120),
                            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                        ]),
                ]),

            Forms\Components\Section::make('F · Roles')->collapsible()->schema([
                Forms\Components\TextInput::make('system_admin')->label('Main system administrator')->maxLength(120),
                Forms\Components\TextInput::make('records_viewers')->label('Who sees records / bills / stock / reports')->maxLength(1000),
                Forms\Components\TextInput::make('approval_authority')->label('Who approves prescriptions / lab results')->maxLength(1000),
            ]),

            Forms\Components\Section::make('G · Documents & data')->columns(3)->collapsible()->collapsed()->schema([
                Forms\Components\FileUpload::make('logo_path')->label('Logo')->image()->disk('local')->directory('onboarding'),
                Forms\Components\FileUpload::make('stamp_path')->label('Stamp')->image()->disk('local')->directory('onboarding'),
                Forms\Components\FileUpload::make('letterhead_path')->label('Letterhead')->image()->disk('local')->directory('onboarding'),
                Forms\Components\FileUpload::make('signed_agreement_path')->label('Signed agreement')->disk('local')->directory('onboarding'),
                Forms\Components\FileUpload::make('service_price_list_path')->label('Service & price list')->disk('local')->directory('onboarding'),
                Forms\Components\FileUpload::make('drug_stock_path')->label('Drug stock')->disk('local')->directory('onboarding'),
                Forms\Components\FileUpload::make('lab_test_list_path')->label('Lab test list')->disk('local')->directory('onboarding'),
                Forms\Components\FileUpload::make('pharmacy_licence_path')->label('Pharmacy licence')->disk('local')->directory('onboarding'),
                Forms\Components\FileUpload::make('lab_licence_path')->label('Lab licence')->disk('local')->directory('onboarding'),
                Forms\Components\Select::make('patient_data_format')->label('Patient records format')->options(static::en(HospitalOnboarding::$patientDataFormats)),
                Forms\Components\TextInput::make('patient_record_count')->numeric()->minValue(0),
                Forms\Components\TextInput::make('patient_data_notes')->maxLength(1000),
            ]),

            Forms\Components\Section::make('Admin')->columns(2)->schema([
                Forms\Components\Select::make('status')->options(static::en(HospitalOnboarding::$statusLabels))->default('new')->required(),
                Forms\Components\TextInput::make('locale')->disabled()->dehydrated(false),
                Forms\Components\Textarea::make('notes')->label('Facility notes')->rows(2)->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('contact_name')->label('Completed by')->searchable(),
                Tables\Columns\TextColumn::make('facility_type')
                    ->formatStateUsing(fn (?string $state) => $state ? (HospitalOnboarding::facilityTypes()[$state]['en'] ?? $state) : '—')
                    ->badge(),
                Tables\Columns\TextColumn::make('staff_count')->counts('staff')->label('Staff')->sortable(),
                Tables\Columns\IconColumn::make('data_sharing_consent')->label('Consent')->boolean(),
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
                Tables\Filters\SelectFilter::make('status')->options(static::en(HospitalOnboarding::$statusLabels)),
                Tables\Filters\SelectFilter::make('facility_type')->options(static::en(HospitalOnboarding::facilityTypes())),
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
