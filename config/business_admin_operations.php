<?php

$class = 'App\\Contexts\\Operations\\Application\\Commands\\AdminMilestoneCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$project = ['name' => 'project', 'kind' => 'model', 'class' => 'App\\Models\\Project', 'key' => 'id'];
$milestone = ['name' => 'milestone', 'kind' => 'model', 'class' => 'App\\Models\\Milestone', 'key' => 'id'];
$projectQueries = 'App\\Contexts\\Operations\\Application\\Queries\\ProjectQueries';
$assetClass = 'App\\Contexts\\Operations\\Application\\Commands\\AdminAssetCommands';
$asset = ['name' => 'asset', 'kind' => 'model', 'class' => 'App\\Models\\Asset', 'key' => 'id'];

return [
    'admin.operations.assets.index' => ['handler' => 'App\\Contexts\\Operations\\Application\\Queries\\AdminAssetQueries', 'method' => 'index', 'parameters' => [$input], 'command' => false,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:read',
        'api_path' => 'operations/assets', 'http_methods' => ['GET'], 'source_controller' => 'AssetResource', 'source_action' => 'index'],
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
    'admin.operations.milestones.index' => ['handler' => $projectQueries, 'method' => 'milestones', 'parameters' => [$input, $project], 'command' => false,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:read',
        'api_path' => 'operations/projects/{project}/milestones', 'http_methods' => ['GET'], 'source_controller' => 'ProjectResource', 'source_action' => 'milestones.index'],
    'admin.operations.milestones.show' => ['handler' => $projectQueries, 'method' => 'milestone', 'parameters' => [$input, $project, $milestone], 'command' => false,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:read',
        'api_path' => 'operations/projects/{project}/milestones/{milestone}', 'http_methods' => ['GET'], 'source_controller' => 'ProjectResource', 'source_action' => 'milestones.show'],
    'admin.operations.milestones.create' => ['handler' => $class, 'method' => 'create', 'parameters' => [$input, $project], 'command' => true,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:write',
        'api_path' => 'operations/projects/{project}/milestones', 'http_methods' => ['POST'], 'source_controller' => 'ProjectResource', 'source_action' => 'milestones.create'],
    'admin.operations.milestones.update' => ['handler' => $class, 'method' => 'update', 'parameters' => [$input, $project, $milestone], 'command' => true,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:write',
        'api_path' => 'operations/projects/{project}/milestones/{milestone}', 'http_methods' => ['PUT'], 'source_controller' => 'ProjectResource', 'source_action' => 'milestones.update'],
    'admin.operations.milestones.delete' => ['handler' => $class, 'method' => 'delete', 'parameters' => [$input, $project, $milestone], 'command' => true,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:write',
        'api_path' => 'operations/projects/{project}/milestones/{milestone}', 'http_methods' => ['DELETE'], 'source_controller' => 'ProjectResource', 'source_action' => 'milestones.delete'],
];
