<?php

namespace App\Contexts\Enquiries\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\WebsiteFormDelivery;

final class AdminWebsiteFormDeliveryQueries extends WorkflowHandler
{
    private const FIELDS = [
        'event_id', 'event_type', 'submission_type', 'submission_id', 'recipient', 'status',
        'attempts', 'next_attempt_at', 'sent_at', 'message_id', 'error_type', 'created_at', 'updated_at',
    ];

    public function index(WorkflowInput $input): WorkflowResult
    {
        $filters = $input->validate([
            'status' => 'sometimes|string|in:pending,processing,sent,failed,uncertain',
            'event_type' => 'sometimes|string|max:80',
            'from' => 'sometimes|date',
            'to' => 'sometimes|date|after_or_equal:from',
        ]);
        $query = WebsiteFormDelivery::query()->select(self::FIELDS)->orderByDesc('created_at')->orderByDesc('id');
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['event_type'])) $query->where('event_type', $filters['event_type']);
        if (isset($filters['from'])) $query->where('created_at', '>=', $filters['from']);
        if (isset($filters['to'])) $query->where('created_at', '<=', $filters['to']);

        $page = $input->paginate($query);
        return WorkflowResult::payload([
            'items' => $page->items(),
            'pagination' => [
                'current_page' => $page->currentPage(), 'per_page' => $page->perPage(),
                'total' => $page->total(), 'last_page' => $page->lastPage(),
            ],
        ]);
    }

    public function show(WorkflowInput $input, string $eventId): WorkflowResult
    {
        $delivery = WebsiteFormDelivery::query()->select(self::FIELDS)->where('event_id', $eventId)->firstOrFail();
        return WorkflowResult::payload(['delivery' => $delivery->toArray()]);
    }
}
