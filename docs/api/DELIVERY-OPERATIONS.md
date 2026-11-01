# Website enquiry delivery operations

The database `website_form_deliveries` table is a durable mail outbox for all four business enquiry forms. It stores versioned event identity, source reference, fixed recipient, claim/attempt metadata and delivery outcome, without copying the message body. `api_idempotency_records` links a hashed public API key to its source and saved acknowledgement. Neither table is publicly queryable.

New submissions attempt delivery immediately after the source and intent commit. Recovery uses atomic database claims. Pending and pre-send failures are safely eligible; backoff starts at 60 seconds and caps at one hour. A thrown exception during mail sending, or an abandoned processing claim after two minutes, becomes `uncertain`. This deliberately requires human reconciliation because the server may already have accepted the message. SMTP acceptance does not prove inbox placement.

## Routine recovery

Run from the application root:

```sh
php artisan website-forms:deliver --retry-only --limit=100
```

It skips successes and future retries, and returns a nonzero exit code when the processed batch contains failed/uncertain outcomes. Investigate these results; do not assume every failed command should be blindly repeated with force options. Historical source reconciliation remains `website-forms:deliver --recover`.

Each new delivery has a stable `X-OHS-Event-ID` mail header and a Message-ID derived from that UUID. Inspect an ambiguous event with `php artisan website-forms:resolve EVENT_UUID`. If its message is confirmed in the contact inbox, record that fact without resending:

```sh
php artisan website-forms:resolve EVENT_UUID --sent
```

If mailbox/transport investigation confirms a resend is needed, `website-forms:deliver --retry-uncertain` explicitly permits ambiguous deliveries to be attempted again. This batch command can resend multiple ambiguous messages and can produce duplicates; review its scope first. A stable Message-ID supports investigation but is not a provider deduplication guarantee.

## Hosting scheduler

The Laravel schedule defines a minute-based retry task, but the current SSH account has no `crontab` executable and no scheduler has been activated. In Hostinger's cron management, a dedicated every-minute command can invoke only this task:

```sh
cd /home/u117099711/domains/opeshealthsystems.com/public_html && /usr/bin/php artisan website-forms:deliver --retry-only --limit=100
```

Configure failure monitoring/log retention and verify an actual cron execution before claiming unattended retries. Invoking all application schedules also runs pre-existing invoice, payout and other jobs; review those separately. No new public HTTP cron endpoint or embedded cron secret is introduced.

## Legacy ledger cutover

Put the app into maintenance mode and pause any existing mail recovery processes. Back up source, database and the private JSON ledger. Run the additive migration, then validate and import the ledger:

```sh
php artisan website-forms:import-ledger
php artisan website-forms:import-ledger --apply
```

Import sends no mail, preserves historical successes, maps interrupted legacy sends to uncertain, refuses invalid/orphaned entries and leaves the private file intact. It does not downgrade outcomes attempted after cutover. Compare legacy success counts with database success records before opening traffic. Run recovery afterward and verify that historical successes are skipped.

## Controlled source rollback

Keep the new database tables and their history. Before restoring the previous mailer code, enter maintenance, pause retry processes and resolve all active/uncertain attempts. Export current outcomes so the previous file-based mailer knows about post-cutover successes:

```sh
php artisan website-forms:export-ledger
php artisan website-forms:export-ledger --apply
```

The export refuses active/uncertain outcomes, writes atomically with restrictive permissions and preserves the previous file. Restore reviewed source and caches, then verify recovery counts before reopening traffic. Do not run migration rollback or restore an old database over new customer submissions as a routine source rollback.
