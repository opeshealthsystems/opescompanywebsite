<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TesterApplicationResource\Pages;
use App\Models\TesterApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TesterApplicationResource extends Resource
{
    protected static ?string $model = TesterApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Practitioners';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationLabel = 'Tester Applications';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Applicant')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->disabledOn('edit')->dehydrated(false)->required(),
                Forms\Components\TextInput::make('email')->email()->disabledOn('edit')->dehydrated(false)->required(),
                Forms\Components\TextInput::make('phone')->disabledOn('edit')->dehydrated(false),
                Forms\Components\Select::make('profession')->disabledOn('edit')->dehydrated(false)->required()->options(collect(TesterApplication::$professions)->map(fn($v) => $v['en'])),
                Forms\Components\TextInput::make('specialty')->disabledOn('edit')->dehydrated(false),
                Forms\Components\TextInput::make('institution_name')->disabledOn('edit')->dehydrated(false),
                Forms\Components\TextInput::make('country')->disabledOn('edit')->dehydrated(false)->required(),
                Forms\Components\TextInput::make('city')->disabledOn('edit')->dehydrated(false),
                Forms\Components\TextInput::make('years_experience')->disabledOn('edit')->dehydrated(false)->numeric()->minValue(0)->maxValue(50),
            ]),
            Forms\Components\Section::make('Technical Profile')->schema([
                Forms\Components\CheckboxList::make('devices')->disabledOn('edit')->dehydrated(false)->options(['smartphone' => 'Smartphone', 'tablet' => 'Tablet', 'laptop' => 'Laptop', 'desktop' => 'Desktop']),
                Forms\Components\CheckboxList::make('platforms')->disabledOn('edit')->dehydrated(false)->options(['android' => 'Android', 'ios' => 'iOS', 'windows' => 'Windows', 'macos' => 'macOS', 'web' => 'Web Browser']),
                Forms\Components\Textarea::make('tech_experience')->disabledOn('edit')->dehydrated(false)->rows(3),
                Forms\Components\Textarea::make('motivation')->disabledOn('edit')->dehydrated(false)->required()->rows(4),
            ]),
            Forms\Components\Section::make('Admin')->columns(2)->schema([
                Forms\Components\Select::make('status')->options(array_combine(TesterApplication::$statuses, TesterApplication::$statuses))->required(),
                Forms\Components\TextInput::make('locale')->disabled()->dehydrated(false),
                Forms\Components\Textarea::make('admin_notes')->columnSpanFull()->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('profession')->badge()->sortable(),
                Tables\Columns\TextColumn::make('country')->sortable(),
                Tables\Columns\TextColumn::make('years_experience')->suffix(' yrs')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'pending', 'success' => 'accepted', 'danger' => 'rejected', 'primary' => 'active']),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(array_combine(TesterApplication::$statuses, TesterApplication::$statuses)),
                Tables\Filters\SelectFilter::make('profession')->options(collect(TesterApplication::$professions)->map(fn($v) => $v['en'])),
            ])
            ->actions([Tables\Actions\EditAction::make()->using(fn (\App\Models\TesterApplication $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.enquiries.testerApplications.update', $data, ['testerApplication' => $record->id]))]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTesterApplications::route('/'),
            'edit'   => Pages\EditTesterApplication::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return app(\App\Contexts\Enquiries\Application\Queries\AdminExternalApplicationQueries::class)->testerQuery(
            new \App\Contexts\Shared\Application\WorkflowInput(actor: auth()->user(), language: app()->getLocale())
        );
    }
}
