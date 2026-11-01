# Website enquiry delivery

Current implementation: the database outbox and idempotency release supersedes the initial file-based tracking described below. See [delivery operations](api/DELIVERY-OPERATIONS.md) and [implementation status](architecture/IMPLEMENTATION-STATUS.md).

Deployed 30 October 2026. Contact/product enquiries, demo requests, partner applications and tester applications send immediately to `contact@opeshealthsystems.com`. Submitter acknowledgements and account/password messages retain their intended recipients.

All 15 previously stored enquiries were forwarded and accepted by SMTP. One clearly labelled technical contact-form submission subsequently verified the live submission path. The recovery command then reported 16 already-sent records and zero failures; it did not resend those records. SMTP acceptance does not establish inbox placement.

Delivery status is recorded privately outside public assets. Recovery skips recorded successes. Failed attempts retain retry status and backoff; the retry command is `php artisan website-forms:deliver --retry-only`. Historical recovery is `php artisan website-forms:deliver --recover`. A timed retry scheduler has not been installed on this hosting account; new submissions send synchronously.

Validation: 9 relevant feature tests passed, with 94 assertions. Deployment backup: `/home/u117099711/releases/ohs-form-routing-20261030/backup-reviewed.tar.gz`.
