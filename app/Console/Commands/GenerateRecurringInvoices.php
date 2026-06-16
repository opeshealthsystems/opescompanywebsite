<?php

namespace App\Console\Commands;

use App\Models\InvoiceTemplate;
use Illuminate\Console\Command;

class GenerateRecurringInvoices extends Command
{
    protected $signature   = 'invoices:generate-recurring {--dry-run : Preview without creating}';
    protected $description = 'Generate invoices from active recurring templates that are due today or overdue';

    public function handle(): int
    {
        $due = InvoiceTemplate::where('is_active', true)
            ->where('next_due_date', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', today());
            })
            ->get();

        if ($due->isEmpty()) {
            $this->info('No recurring invoices due.');
            return self::SUCCESS;
        }

        $this->info("Found {$due->count()} template(s) due for generation.");
        $generated = 0;
        $skipped   = 0;

        foreach ($due as $template) {
            if ($this->option('dry-run')) {
                $this->line("  [DRY] Would generate from: {$template->name} (due {$template->next_due_date->format('d M Y')})");
                continue;
            }

            $invoice = $template->generateInvoice();

            if ($invoice) {
                $this->line("  \u{2713} Generated {$invoice->invoice_number} from: {$template->name}");
                $generated++;
            } else {
                $this->warn("  \u{2717} Skipped: {$template->name} (inactive or past end date)");
                $skipped++;
            }
        }

        if (!$this->option('dry-run')) {
            $this->info("Done. Generated: {$generated}, Skipped: {$skipped}");
        }

        return self::SUCCESS;
    }
}
