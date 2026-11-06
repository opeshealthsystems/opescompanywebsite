<?php

namespace App\Filament\Resources\PartnerApplicationResource\RelationManagers;

use App\Contexts\Enquiries\Application\Queries\PartnerAccountQueries;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Models\PartnerApplicationMembership;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'memberships';
    protected static ?string $title = 'Partner account access';

    protected function getTableQuery(): Builder
    {
        return AdminWorkflowAdapter::query(PartnerAccountQueries::class, 'memberQuery', [$this->getOwnerRecord()]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')->label('Verified partner account')->required()->searchable()->preload(false)
                ->disabledOn('edit')->dehydrated(fn (?string $operation) => $operation !== 'edit')
                ->getSearchResultsUsing(fn (string $search) => AdminWorkflowAdapter::query(PartnerAccountQueries::class, 'eligibleMemberQuery')
                    ->where(fn ($users) => $users->where('name', 'like', '%'.trim($search).'%')->orWhere('email', 'like', '%'.trim($search).'%'))
                    ->limit(50)->get()->mapWithKeys(fn ($user) => [$user->id => $user->name.' · '.$user->email])->all())
                ->getOptionLabelUsing(fn ($value) => AdminWorkflowAdapter::query(PartnerAccountQueries::class, 'eligibleMemberQuery')->find($value)?->name),
            Select::make('membership_role')->label('Organization role')->required()->options(['owner' => 'Owner', 'member' => 'Member'])->default('member'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.name')->label('Name')->searchable(),
            TextColumn::make('user.email')->label('Email')->searchable(),
            TextColumn::make('membership_role')->label('Access')->badge()->color(fn ($state) => $state === 'owner' ? 'primary' : 'gray'),
            TextColumn::make('created_at')->label('Added')->dateTime()->sortable(),
        ])->defaultSort('id', 'asc')->headerActions([
            Tables\Actions\CreateAction::make()->using(function (array $data) {
                AdminWorkflowAdapter::run('admin.enquiries.partnerApplications.members.add', $data, ['partnerApplication' => $this->getOwnerRecord()->id]);
                return AdminWorkflowAdapter::query(PartnerAccountQueries::class, 'memberQuery', [$this->getOwnerRecord()])
                    ->where('user_id', $data['user_id'])->firstOrFail();
            })->visible(fn () => $this->getOwnerRecord()->status === 'approved'),
        ])->actions([
            Tables\Actions\EditAction::make()->using(function (PartnerApplicationMembership $record, array $data) {
                AdminWorkflowAdapter::run('admin.enquiries.partnerApplications.members.add', ['user_id' => $record->user_id, 'membership_role' => $data['membership_role']], ['partnerApplication' => $this->getOwnerRecord()->id]);
                return AdminWorkflowAdapter::query(PartnerAccountQueries::class, 'memberQuery', [$this->getOwnerRecord()])->whereKey($record->id)->firstOrFail();
            }),
            Tables\Actions\DeleteAction::make()->using(function (PartnerApplicationMembership $record) {
                AdminWorkflowAdapter::run('admin.enquiries.partnerApplications.members.remove', parameters: ['partnerApplication' => $this->getOwnerRecord()->id, 'member' => $record->user_id]);
                return true;
            }),
        ]);
    }
}
