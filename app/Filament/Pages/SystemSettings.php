<?php

namespace App\Filament\Pages;

use App\Models\CompanySetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SystemSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'System Settings';
    protected static ?string $navigationGroup = 'Platform';
    protected static ?int $navigationSort = 99;
    protected static string $view = 'filament.pages.system-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function mount(): void
    {
        $settings = CompanySetting::current();
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('company_email')
                            ->label('Company Email')
                            ->email()
                            ->nullable(),
                        Forms\Components\TextInput::make('company_phone')
                            ->label('Company Phone')
                            ->nullable(),
                        Forms\Components\TextInput::make('company_website')
                            ->label('Website')
                            ->nullable()
                            ->url(),
                        Forms\Components\Textarea::make('company_address')
                            ->label('Address')
                            ->rows(2)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Localization')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('timezone')
                            ->options(CompanySetting::timezoneOptions())
                            ->required(),
                        Forms\Components\Select::make('default_currency')
                            ->label('Default Currency')
                            ->options([
                                'XAF' => 'XAF (CFA Franc)',
                                'USD' => 'USD (US Dollar)',
                                'EUR' => 'EUR (Euro)',
                                'GBP' => 'GBP (British Pound)',
                            ])
                            ->required(),
                        Forms\Components\Select::make('fiscal_year_start_month')
                            ->label('Fiscal Year Start')
                            ->options(CompanySetting::monthOptions())
                            ->required(),
                        Forms\Components\TextInput::make('date_format')
                            ->label('Date Format')
                            ->maxLength(30)
                            ->placeholder('d M Y'),
                    ]),

                Forms\Components\Section::make('Billing & Documents')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('invoice_prefix')
                            ->label('Invoice Prefix')
                            ->maxLength(20)
                            ->placeholder('INV'),
                        Forms\Components\TextInput::make('quote_prefix')
                            ->label('Quote Prefix')
                            ->maxLength(20)
                            ->placeholder('QTE'),
                        Forms\Components\TextInput::make('default_tax_rate')
                            ->label('Default Tax Rate (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                    ]),

                Forms\Components\Section::make('Logo')
                    ->schema([
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Company Logo')
                            ->image()
                            ->directory('company')
                            ->nullable(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = CompanySetting::current();
        $settings->update($this->form->getState());

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('success'),
        ];
    }
}
