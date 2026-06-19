<?php

namespace App\Filament\Resources\IssueReportResource\Pages;

use App\Filament\Resources\IssueReportResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListIssueReports extends ListRecords
{
    protected static string $resource = IssueReportResource::class;

    public function getTabs(): array
    {
        return [
            'all'                 => Tab::make('All'),
            'submitted'           => Tab::make('Submitted')->modifyQueryUsing(fn ($q) => $q->where('status', 'submitted')),
            'clinical_review'     => Tab::make('Clinical Review')->modifyQueryUsing(fn ($q) => $q->where('status', 'clinical_review')),
            'product_review'      => Tab::make('Product Review')->modifyQueryUsing(fn ($q) => $q->where('status', 'product_review')),
            'accepted'            => Tab::make('Accepted')->modifyQueryUsing(fn ($q) => $q->where('status', 'accepted')),
            'sent_to_development' => Tab::make('Sent to Development')->modifyQueryUsing(fn ($q) => $q->where('status', 'sent_to_development')),
            'ready_for_retest'    => Tab::make('Ready for Retest')->modifyQueryUsing(fn ($q) => $q->where('status', 'ready_for_retest')),
            'closed'              => Tab::make('Closed')->modifyQueryUsing(fn ($q) => $q->where('status', 'closed')),
        ];
    }
}
