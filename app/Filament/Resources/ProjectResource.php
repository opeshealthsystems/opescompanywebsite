<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\MilestonesRelationManager;
use App\Models\Project;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Projects';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['planning','active'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'info'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Project')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')
                    ->disabled()->placeholder('Auto-generated'),

                Forms\Components\Select::make('status')
                    ->options(Project::statusOptions())->default('planning')->required(),

                Forms\Components\TextInput::make('title')
                    ->required()->maxLength(250)->columnSpanFull(),

                Forms\Components\Select::make('priority')
                    ->options(Project::priorityOptions())->default('medium')->required(),

                Forms\Components\Select::make('owner_id')
                    ->label('Project Owner')
                    ->options(fn () => User::orderBy('name')->pluck('name','id'))
                    ->default(fn () => auth()->id())
                    ->searchable()->required(),

                Forms\Components\DatePicker::make('start_date')->native(false)->nullable(),
                Forms\Components\DatePicker::make('end_date')->native(false)->nullable(),

                Forms\Components\TextInput::make('budget')->numeric()->default(0)->minValue(0),
                Forms\Components\Select::make('currency')
                    ->options(['XAF'=>'XAF','USD'=>'USD','EUR'=>'EUR'])->default('XAF'),
            ]),

            Forms\Components\Section::make('Description & Notes')->collapsible()->collapsed()->schema([
                Forms\Components\Textarea::make('description')->rows(3)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->label('Internal Notes')->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->fontFamily('mono')->copyable()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->weight('semibold'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'success','planning' => 'info','on_hold' => 'warning',
                        'completed' => 'success','cancelled' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn ($state) => match($state) {
                        'critical' => 'danger','high' => 'warning','medium' => 'info', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('owner.name')->label('Owner')->sortable(),
                Tables\Columns\TextColumn::make('end_date')->label('Deadline')->date('d M Y')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('milestones_count')
                    ->label('Milestones')->counts('milestones')->alignCenter()->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Project::statusOptions()),
                Tables\Filters\SelectFilter::make('priority')->options(Project::priorityOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Project Details')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')->fontFamily('mono')->copyable(),
                Infolists\Components\TextEntry::make('title')->columnSpan(2),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'success','planning' => 'info','on_hold' => 'warning',
                        'completed' => 'success','cancelled' => 'danger', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('priority')->badge()
                    ->color(fn ($state) => match($state) {
                        'critical' => 'danger','high' => 'warning','medium' => 'info', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('owner.name')->label('Project Owner'),
            ]),
            Infolists\Components\Section::make('Timeline & Budget')->columns(4)->schema([
                Infolists\Components\TextEntry::make('start_date')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('end_date')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('budget')
                    ->getStateUsing(fn ($record) => $record->currency . ' ' . number_format((float)$record->budget, 0)),
                Infolists\Components\TextEntry::make('currency'),
            ]),
            Infolists\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Infolists\Components\TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                Infolists\Components\TextEntry::make('notes')->label('Internal Notes')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [MilestonesRelationManager::class];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'title'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view'   => Pages\ViewProject::route('/{record}'),
            'edit'   => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
