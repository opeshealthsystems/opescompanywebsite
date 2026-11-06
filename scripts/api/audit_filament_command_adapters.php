<?php

declare(strict_types=1);

/** Ensure Filament writes and custom action callbacks delegate to shared application workflows. */
$root = dirname(__DIR__, 2);
$files = [];
$directory = $root.'/app/Filament';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

$writeMethods = ['handleRecordCreation' => 'CreateRecord', 'handleRecordUpdate' => 'EditRecord'];
$audited = ['CreateRecord' => 0, 'EditRecord' => 0];
$unmapped = [];
$directPersistence = [];
$actionFiles = 0;
$unmappedActionFiles = [];
$directPersistenceMethods = ['save', 'update', 'delete', 'forceDelete', 'forceDeleteQuietly', 'insert', 'insertOrIgnore',
    'upsert', 'updateOrCreate', 'firstOrCreate', 'updateOrInsert', 'increment', 'decrement'];

foreach ($files as $file) {
    $source = file_get_contents($file);
    if (preg_match('/->\s*(?:action|using)\s*\(/', $source) === 1) {
        $actionFiles++;
        if (preg_match('/AdminWorkflowAdapter::|WorkflowDispatcher|Application\\\\Commands/', $source) !== 1) {
            $unmappedActionFiles[] = str_replace($root.'/', '', $file);
        }
    }

    $tokens = token_get_all($source);
    foreach ($tokens as $index => $token) {
        if (! is_array($token) || ! in_array($token[0], [T_OBJECT_OPERATOR, T_DOUBLE_COLON], true)) {
            continue;
        }
        $nameIndex = $index + 1;
        while ($nameIndex < count($tokens) && is_array($tokens[$nameIndex]) && in_array($tokens[$nameIndex][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
            $nameIndex++;
        }
        if (isset($tokens[$nameIndex][0]) && $tokens[$nameIndex][0] === T_STRING
            && in_array($tokens[$nameIndex][1], $directPersistenceMethods, true)) {
            $directPersistence[] = str_replace($root.'/', '', $file).':'.$token[2].':'.$tokens[$nameIndex][1];
        }
    }

    foreach ($writeMethods as $method => $pageBase) {
        if (! preg_match('/\bclass\s+\w+\s+extends\s+(?:[\\\\\w]+\\\\)?'.preg_quote($pageBase, '/').'\b/', $source)) {
            continue;
        }

        $count = count($tokens);
        $found = false;
        for ($index = 0; $index < $count; $index++) {
            if (! is_array($tokens[$index]) || $tokens[$index][0] !== T_FUNCTION) {
                continue;
            }
            $nameIndex = $index + 1;
            while ($nameIndex < $count && is_array($tokens[$nameIndex]) && in_array($tokens[$nameIndex][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                $nameIndex++;
            }
            if (! isset($tokens[$nameIndex][0]) || $tokens[$nameIndex][0] !== T_STRING || $tokens[$nameIndex][1] !== $method) {
                continue;
            }

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
            $found = preg_match('/Application\\\\Commands|AdminWorkflowAdapter::(?:record|run)|WorkflowDispatcher/', $body) === 1;
            break;
        }

        $audited[$pageBase]++;
        if (! $found) {
            $unmapped[] = str_replace($root.'/', '', $file).':'.$method;
        }
    }
}

echo json_encode([
    'create_edit_pages' => $audited,
    'unmapped' => $unmapped,
    'direct_persistence_calls' => $directPersistence,
    'custom_action_files' => $actionFiles,
    'unmapped_custom_action_files' => $unmappedActionFiles,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;

exit($unmapped || $directPersistence || $unmappedActionFiles ? 1 : 0);
