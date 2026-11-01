<?php

namespace App\Filament\Pages;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Pages\Page;

class ContractsExpiry extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Contracts Expiry';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 98;
    protected static string $view = 'filament.pages.contracts-expiry';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getContractData(): array
    {
        return array_merge(...array_map(fn (array $bucket) => $bucket['rows'], array_values($this->getExpiryBuckets())));
    }

    public function getExpiryBuckets(): array
    {
        return app(WorkflowDispatcher::class)->execute('admin.reports.contractsExpiry',
            new WorkflowInput(actor: auth()->user(), language: app()->getLocale()))->data['buckets'];
    }
}
