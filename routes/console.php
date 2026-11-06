<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send license expiry warnings at 08:00 every day
Schedule::command('licenses:send-expiry-warnings')->dailyAt('08:00');

// Generate invoices from active recurring templates that are due
Schedule::command('invoices:generate-recurring')->dailyAt('06:00');

// Send contract expiry warnings (30-day and 7-day notices)
Schedule::command('contracts:send-expiry-warnings')->dailyAt('08:30');

// Send training certification expiry warnings
Schedule::command('training:send-expiry-warnings')->dailyAt('09:00');

// Confirm pending mobile-money payouts
Schedule::command('payouts:poll')->everyFiveMinutes()->withoutOverlapping();

// Requires a hosting scheduler invoking artisan schedule:run every minute.
Schedule::command('website-forms:deliver --retry-only --limit=100')->everyMinute()->withoutOverlapping();
Schedule::command('business:outbox --limit=100')->everyMinute()->withoutOverlapping();

Artisan::command('api:metrics-prune {--days=35}', function (): void {
    $days = max(31, min(90, (int) $this->option('days')));
    $deleted = \Illuminate\Support\Facades\DB::table('api_request_metrics')
        ->where('metric_date', '<', now('UTC')->subDays($days)->toDateString())
        ->delete();
    $this->info("Pruned {$deleted} API metric aggregates older than {$days} days.");
})->purpose('Remove API request metric aggregates beyond the configured retention window');
