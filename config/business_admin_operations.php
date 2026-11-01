<?php

$class = 'App\\Contexts\\Operations\\Application\\Commands\\AdminMilestoneCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$milestone = ['name' => 'milestone', 'kind' => 'model', 'class' => 'App\\Models\\Milestone', 'key' => 'id'];
$assetClass = 'App\\Contexts\\Operations\\Application\\Commands\\AdminAssetCommands';
$asset = ['name' => 'asset', 'kind' => 'model', 'class' => 'App\\Models\\Asset', 'key' => 'id'];

return [
    'admin.operations.assets.create' => ['handler' => $assetClass, 'method' => 'create', 'parameters' => [$input], 'command' => true,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:write',
        'api_path' => 'operations/assets', 'http_methods' => ['POST'], 'source_controller' => 'AssetResource', 'source_action' => 'create'],
    'admin.operations.assets.update' => ['handler' => $assetClass, 'method' => 'update', 'parameters' => [$input, $asset], 'command' => true,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:write',
        'api_path' => 'operations/assets/{asset}', 'http_methods' => ['PUT'], 'source_controller' => 'AssetResource', 'source_action' => 'update'],
    'admin.operations.assets.delete' => ['handler' => $assetClass, 'method' => 'delete', 'parameters' => [$input, $asset], 'command' => true,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:write',
        'api_path' => 'operations/assets/{asset}', 'http_methods' => ['DELETE'], 'source_controller' => 'AssetResource', 'source_action' => 'delete'],
    'admin.workforce.milestones.complete' => ['handler' => $class, 'method' => 'complete', 'parameters' => [$input, $milestone], 'command' => true,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:write',
        'api_path' => 'operations/milestones/{milestone}/complete', 'http_methods' => ['POST'],
        'source_controller' => 'ProjectResource', 'source_action' => 'complete_milestone'],
];
