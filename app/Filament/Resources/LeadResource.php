<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers\DealsRelationManager;
use App\Filament\Resources\LeadResource\RelationManagers\QuotesRelationManager;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Enquiries & Integration Requests';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact Details')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(100),
                Forms\Components\TextInput::make('email')->email()->required()->maxLength(150),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(30),
                Forms\Components\Select::make('facility_type')->options([
                    'clinic'      => 'Clinic',
                    'hospital'    => 'Hospital',
                    'laboratory'  => 'Laboratory',
                    'pharmacy'    => 'Pharmacy',
                    'government'  => 'Government / Ministry',
                    'ngo'         => 'NGO / International',
                    'API integration' => 'API integration',
                    'other'       => 'Other',
                ])->disabled(fn (?Lead $record) => $record?->source === 'integration-request')
                    ->dehydrated(fn (?Lead $record) => $record?->source !== 'integration-request'),
            ])->columns(2),

            Forms\Components\Section::make('Inquiry')->schema([
                Forms\Components\TextInput::make('products')->label('Products of interest')->maxLength(255),
                Forms\Components\Textarea::make('message')->rows(4)->columnSpanFull(),
                Forms\Components\Placeholder::make('integration_application.application_name')->label('Integration application')->content(fn (?Lead $record) => data_get($record?->integration_application, 'application_name', '—')),
                Forms\Components\Placeholder::make('integration_application.access_type')->label('Requested access')->content(fn (?Lead $record) => data_get($record?->integration_application, 'access_type', '—')),
                Forms\Components\Placeholder::make('integration_application.redirect_uri')->label('OAuth callback')->content(fn (?Lead $record) => data_get($record?->integration_application, 'redirect_uri', '—')),
                Forms\Components\Placeholder::make('integration_application.requested_scopes')->label('Requested scopes')->content(fn (?Lead $record) => implode(', ', data_get($record?->integration_application, 'requested_scopes', []))),
            ]),

            Forms\Components\Section::make('Meta')->schema([
                Forms\Components\Select::make('status')->options([
                    'new'       => 'New',
                    'contacted' => 'Contacted',
                    'qualified' => 'Qualified',
                    'closed'    => 'Closed / Won',
                ])->required(),
                Forms\Components\Placeholder::make('source')->label('Source')->content(fn (?Lead $record) => $record?->source ?? 'Created in admin'),
                Forms\Components\TextInput::make('product_slug')->label('Product slug'),
                Forms\Components\TextInput::make('locale')->label('Language'),
            ])->columns(2),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Contact Details')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('name'),
                    Infolists\Components\TextEntry::make('email')->copyable(),
                    Infolists\Components\TextEntry::make('phone')->placeholder('—'),
                    Infolists\Components\TextEntry::make('facility_type')
                        ->label('Facility Type')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'clinic'     => 'Clinic',
                            'hospital'   => 'Hospital',
                            'laboratory' => 'Laboratory',
                            'pharmacy'   => 'Pharmacy',
                            'government' => 'Government / Ministry',
                            'ngo'        => 'NGO / International',
                            default      => ucfirst((string) ($state ?? '')),
                        }),
                ]),

            Infolists\Components\Section::make('Inquiry & Status')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('products')
                        ->label('Products of Interest')
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'new'       => 'info',
                            'contacted' => 'warning',
                            'qualified' => 'success',
                            'closed'    => 'gray',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'new'       => 'New',
                            'contacted' => 'Contacted',
                            'qualified' => 'Qualified',
                            'closed'    => 'Closed / Won',
                            default     => ucfirst((string) ($state ?? '')),
                        }),
                    Infolists\Components\TextEntry::make('source')
                        ->placeholder('—')
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'contact'      => 'Contact page',
                            'product-page' => 'Product page',
                            'demo'         => 'Demo CTA',
                            'integration-request' => 'Developer access request',
                            default        => $state ?? '—',
                        }),
                    Infolists\Components\TextEntry::make('product_slug')->label('Product')->placeholder('—'),
                    Infolists\Components\TextEntry::make('locale')->label('Language')->badge()->placeholder('—'),
                    Infolists\Components\TextEntry::make('message')->placeholder('No message provided')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('integration_application.application_name')->label('Application')->placeholder('—'),
                    Infolists\Components\TextEntry::make('integration_application.access_type')->label('Requested access')->badge()->placeholder('—'),
                    Infolists\Components\TextEntry::make('integration_application.redirect_uri')->label('OAuth callback')->copyable()->placeholder('—')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('integration_application.requested_scopes')->label('Requested scopes')->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : '—')->placeholder('—')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('created_at')->label('Received')->dateTime('d M Y H:i'),
                    Infolists\Components\TextEntry::make('updated_at')->label('Last Updated')->dateTime('d M Y H:i'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('facility_type')->label('Facility')->badge(),
                Tables\Columns\TextColumn::make('products')->label('Products')->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'new'       => 'info',
                        'contacted' => 'warning',
                        'qualified' => 'success',
                        'closed'    => 'gray',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('locale')->label('Lang')->badge(),
                Tables\Columns\TextColumn::make('created_at')->label('Received')
                    ->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'new'       => 'New',
                    'contacted' => 'Contacted',
                    'qualified' => 'Qualified',
                    'closed'    => 'Closed',
                ]),
                Tables\Filters\SelectFilter::make('facility_type')->label('Facility type')->options([
                    'clinic'     => 'Clinic',
                    'hospital'   => 'Hospital',
                    'laboratory' => 'Laboratory',
                    'pharmacy'   => 'Pharmacy',
                    'government' => 'Government',
                    'ngo'        => 'NGO',
                    'API integration' => 'API integration',
                ]),
                Tables\Filters\SelectFilter::make('source')->options([
                    'contact' => 'Contact page', 'product-page' => 'Product page', 'demo' => 'Demo CTA',
                    'integration-request' => 'Developer access request',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->using(fn (Lead $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.enquiries.leads.update', $data, ['lead' => $record->id])),
                Tables\Actions\DeleteAction::make()->action(fn (Lead $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run(
                    'admin.enquiries.leads.delete', parameters: ['lead' => $record],
                )),
            ])
            ->bulkActions([]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return app(\App\Contexts\Enquiries\Application\Queries\AdminLeadQueries::class)->leadQuery(
            new \App\Contexts\Shared\Application\WorkflowInput(actor: auth()->user(), language: app()->getLocale())
        );
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) (\App\Contexts\Shared\Infrastructure\AdminReferenceOptions::data('admin.resourceSummaries.metric', ['metric' => 'leads.new'])['count'] ?? 0) ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getRelations(): array
    {
        return [
            QuotesRelationManager::class,
            DealsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view'   => Pages\ViewLead::route('/{record}'),
            'edit'   => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
