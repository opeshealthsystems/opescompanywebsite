<?php

declare(strict_types=1);

/** Ensure Filament list/relation reads are adapters to shared application queries. */
$root = dirname(__DIR__, 2);
$files = [];
foreach (['app/Filament/Resources', 'app/Filament/Pages'] as $directory) {
    $path = $root.'/'.$directory;
    if (! is_dir($path)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
}

$readMethods = ['getEloquentQuery', 'getTableQuery'];
$unmapped = [];
$audited = 0;

foreach ($files as $file) {
    $tokens = token_get_all(file_get_contents($file));
    $count = count($tokens);

    for ($index = 0; $index < $count; $index++) {
        if (! is_array($tokens[$index]) || $tokens[$index][0] !== T_FUNCTION) {
            continue;
        }

        $nameIndex = $index + 1;
        while ($nameIndex < $count && is_array($tokens[$nameIndex]) && in_array($tokens[$nameIndex][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
            $nameIndex++;
        }

        if (! isset($tokens[$nameIndex][0]) || $tokens[$nameIndex][0] !== T_STRING || ! in_array($tokens[$nameIndex][1], $readMethods, true)) {
            continue;
        }

        $method = $tokens[$nameIndex][1];
        $openIndex = $nameIndex + 1;
        while ($openIndex < $count && $tokens[$openIndex] !== '{') {
            $openIndex++;
        }
        if ($openIndex >= $count) {
            continue;
        }

        $depth = 1;
        $body = '';
        for ($bodyIndex = $openIndex + 1; $bodyIndex < $count && $depth > 0; $bodyIndex++) {
            $token = $tokens[$bodyIndex];
            if ($token === '{') {
                $depth++;
            } elseif ($token === '}') {
                $depth--;
            }
            if ($depth > 0) {
                $body .= is_array($token) ? $token[1] : $token;
            }
        }

        $audited++;
        $usesQueryBoundary = preg_match('/Application\\\\Queries|AdminWorkflowAdapter::query|WorkflowDispatcher/', $body) === 1;
        $usesCommandBoundary = preg_match('/Application\\\\Commands/', $body) === 1;
        if (! $usesQueryBoundary || $usesCommandBoundary) {
            $unmapped[] = str_replace($root.'/', '', $file).':'.$method;
        }
    }
}

echo json_encode([
    'filament_read_methods' => $audited,
    'unmapped' => $unmapped,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;

exit($unmapped ? 1 : 0);
