<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTemplateResource\Pages;
use App\Models\DocumentTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentTemplateResource extends Resource
{
    protected static ?string $model = DocumentTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Template Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(150)
                    ->columnSpanFull(),

                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'receipt'           => 'Receipt',
                        'letterhead'        => 'Letterhead',
                        'contract_employee' => 'Employee Contract',
                        'contract_business' => 'Business Contract',
                    ]),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Template Variables')
                ->description('List the placeholder variable names used in the body (e.g. customer_name, amount). These appear as {{variable_name}} in the body.')
                ->schema([
                    Forms\Components\TagsInput::make('variables')
                        ->label('Variables')
                        ->columnSpanFull()
                        ->helperText('Press Enter after each variable name. Use snake_case (e.g. customer_name)'),
                ]),

            Forms\Components\Section::make('Template Body')
                ->description('HTML body of the document. Use {{variable_name}} placeholders. Plain HTML with inline styles is recommended for PDF compatibility.')
                ->schema([
                    Forms\Components\Textarea::make('body')
                        ->required()
                        ->rows(25)
                        ->columnSpanFull()
                        ->extraAttributes(['style' => 'font-family: monospace; font-size: 0.8125rem;']),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'receipt'           => 'success',
                        'letterhead'        => 'info',
                        'contract_employee' => 'warning',
                        'contract_business' => 'danger',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => DocumentTemplate::typeLabel($state)),

                Tables\Columns\TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label('Used')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'receipt'           => 'Receipt',
                        'letterhead'        => 'Letterhead',
                        'contract_employee' => 'Employee Contract',
                        'contract_business' => 'Business Contract',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (DocumentTemplate $record) => ($record->documents_count ?? 0) > 0),
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
            'index'  => Pages\ListDocumentTemplates::route('/'),
            'create' => Pages\CreateDocumentTemplate::route('/create'),
            'edit'   => Pages\EditDocumentTemplate::route('/{record}/edit'),
        ];
    }
}
