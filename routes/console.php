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
