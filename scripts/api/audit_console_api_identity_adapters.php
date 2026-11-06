<?php

declare(strict_types=1);

/** Keep OAuth client CLI commands as adapters over the same application workflows as HTTP. */
$root = dirname(__DIR__, 2);
$adapters = [
    'app/Console/Commands/CreateUserApiClient.php' => ['admin.oauthClients.create'],
    'app/Console/Commands/ManageApiClients.php' => [
        'admin.apiClients.index', 'admin.apiClients.create', 'admin.apiClients.rotate', 'admin.apiClients.revoke',
    ],
];
$directMutations = [
    'createAuthorizationCodeGrantClient(', 'createClientCredentialsGrantClient(',
    '->save(', '->update(', 'tokens()->update(', '->revoked =', '->secret =', '$client->scopes =',
];
$failures = [];

foreach ($adapters as $relative => $operations) {
    $path = $root.'/'.$relative;
    $source = file_get_contents($path);
    if ($source === false || !str_contains($source, 'WorkflowDispatcher')) {
        $failures[] = "$relative does not inject WorkflowDispatcher";
        continue;
    }
    $dynamicDispatch = preg_match('/->execute\(\$operation,\s*\$input\)/', $source) === 1;
    foreach ($operations as $operation) {
        if (!str_contains($source, "'$operation'") || (!str_contains($source, "execute('$operation'") && !$dynamicDispatch)) {
            $failures[] = "$relative does not dispatch or map $operation";
        }
    }
    foreach ($directMutations as $mutation) {
        if (str_contains($source, $mutation)) {
            $failures[] = "$relative performs direct persistence ($mutation)";
        }
    }
}

if ($failures !== []) {
    fwrite(STDERR, implode(PHP_EOL, $failures).PHP_EOL);
    exit(1);
}

printf("Audited %d API identity CLI adapter classes and %d shared workflows; zero direct client persistence calls.\n",
    count($adapters), array_sum(array_map('count', $adapters)));
