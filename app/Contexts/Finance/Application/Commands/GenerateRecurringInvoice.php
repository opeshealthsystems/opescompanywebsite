<?php

namespace App\Contexts\Finance\Application\Commands;

use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Invoice;
use App\Models\InvoiceTemplate;
use Illuminate\Support\Facades\DB;

/** Shared recurring-invoice use case for administrator and scheduled adapters. */
final class GenerateRecurringInvoice
{
    public function execute(int $templateId, ?int $actorId = null): ?Invoice
    {
        return DB::transaction(function () use ($templateId, $actorId): ?Invoice {
            $template = InvoiceTemplate::query()->lockForUpdate()->find($templateId);
            if (!$template
                || !$template->is_active
                || ($template->end_date && $template->end_date->isBefore(today()))
                || $template->next_due_date->isAfter(today())) {
                return null;
            }

            $invoice = $template->generateInvoice();
            if (!$invoice) return null;

            DomainOutbox::append('event', 'finance.invoice_generated.v1', ['references' => [
                ['resource' => 'InvoiceTemplate', 'id' => $template->id],
                ['resource' => 'Invoice', 'id' => $invoice->id],
            ]], $actorId);

            return $invoice;
        }, 3);
    }
}
