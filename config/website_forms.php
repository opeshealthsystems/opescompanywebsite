<?php
return [
    'recipient' => env('WEBSITE_FORMS_EMAIL', 'contact@opeshealthsystems.com'),
    'ledger_path' => env('APP_ENV') === 'testing'
        ? storage_path('framework/testing/form-mail-'.bin2hex(random_bytes(8)).'.json')
        : storage_path('app/private/website-form-deliveries.json'),
];
