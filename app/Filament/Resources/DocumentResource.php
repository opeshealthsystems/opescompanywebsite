<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 11;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'support']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Document Setup')->schema([
                Forms\Components\Select::make('document_template_id')
                    ->label('Template')
                    ->options(
                        DocumentTemplate::where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn ($t) => [$t->id => "[{$t->type}] {$t->name}"])
                    )
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (!$state) return;
                        $template = DocumentTemplate::find($state);
                        if ($template) {
                            $set('type', $template->type);
                            $set('title', $template->name);
                        }
                    }),

                Forms\Components\Hidden::make('type'),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(200),

                Forms\Components\DatePicker::make('valid_until')
                    ->label('Valid Until')
                    ->nullable(),

                Forms\Components\Toggle::make('requires_signature')
                    ->label('Requires Digital Signature')
                    ->default(false),
            ])->columns(2),

            Forms\Components\Section::make('Recipient')->schema([
                Forms\Components\Select::make('addressee_user_id')
                    ->label('System User (Customer / Employee)')
                    ->options(User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (!$state) return;
                        $user = User::find($state);
                        if ($user) {
                            $set('addressee_name', $user->name);
                            $set('addressee_email', $user->email);
                        }
                    }),

                Forms\Components\TextInput::make('addressee_name')
                    ->label('Recipient Name')
                    ->required()
                    ->maxLength(150),

                Forms\Components\TextInput::make('addressee_email')
                    ->label('Recipient Email')
                    ->email()
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Template Variables')
                ->description('Fill in values for each template placeholder.')
                ->schema([
                    Forms\Components\KeyValue::make('variable_values')
                        ->label('Variable Values')
                        ->columnSpanFull()
                        ->keyLabel('Variable')
                        ->valueLabel('Value')
                        ->reorderable(false)
                        ->helperText('These values will replace {{variable_name}} placeholders in the template body.'),
                ]),

            Forms\Components\Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'receipt'           => 'success',
                        'letterhead'        => 'info',
                        'contract_employee' => 'warning',
                        'contract_business' => 'danger',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => DocumentTemplate::typeLabel($state)),

                Tables\Columns\TextColumn::make('addressee_name')
                    ->label('Recipient')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'signed'            => 'success',
                        'pending_signature' => 'warning',
                        'voided'            => 'danger',
                        'sent'              => 'primary',
                        default             => 'gray',
                    }),

                Tables\Columns\IconColumn::make('requires_signature')
                    ->label('Sig. Required')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Issued')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'receipt'           => 'Receipt',
                        'letterhead'        => 'Letterhead',
                        'contract_employee' => 'Employee Contract',
                        'contract_business' => 'Business Contract',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'             => 'Draft',
                        'sent'              => 'Sent',
                        'pending_signature' => 'Pending Signature',
                        'signed'            => 'Signed',
                        'voided'            => 'Voided',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Document $record) => route('documents.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('void')
                    ->label('Void')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn (Document $record) => in_array($record->status, ['signed', 'voided']))
                    ->action(fn (Document $record) => $record->update(['status' => 'voided'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view'   => Pages\ViewDocument::route('/{record}'),
        ];
    }
}
