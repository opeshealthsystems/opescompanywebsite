<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$application = ['name' => 'partnerApplication', 'kind' => 'model', 'class' => 'App\\Models\\PartnerApplication', 'key' => 'id'];
$member = ['name' => 'member', 'kind' => 'model', 'class' => 'App\\Models\\User', 'key' => 'id'];
$queries = 'App\\Contexts\\Enquiries\\Application\\Queries\\PartnerAccountQueries';
$commands = 'App\\Contexts\\Enquiries\\Application\\Commands\\PartnerAccountMembershipCommands';
$accountCommands = 'App\\Contexts\\Enquiries\\Application\\Commands\\PartnerAccountCommands';

return [
    'partner.accounts.index' => ['handler' => $queries, 'method' => 'accounts', 'parameters' => [$input], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['partner'], 'authenticated' => true, 'scope' => 'identity:read',
        'api_path' => 'partner/accounts', 'http_methods' => ['GET'], 'source_controller' => 'PartnerAccountResource', 'source_action' => 'index'],
    'partner.accounts.update' => ['handler' => $accountCommands, 'method' => 'update', 'parameters' => [$input, $application], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['partner'], 'authenticated' => true, 'scope' => 'partner:write',
        'api_path' => 'partner/accounts/{partnerApplication}', 'http_methods' => ['PATCH'], 'source_controller' => 'PartnerAccountResource', 'source_action' => 'update'],
    'partner.accounts.members.index' => ['handler' => $queries, 'method' => 'members', 'parameters' => [$input, $application], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['partner'], 'authenticated' => true, 'scope' => 'partner:read',
        'api_path' => 'partner/accounts/{partnerApplication}/members', 'http_methods' => ['GET'], 'source_controller' => 'PartnerAccountMemberResource', 'source_action' => 'index'],
    'partner.accounts.members.add' => ['handler' => $commands, 'method' => 'addMember', 'parameters' => [$input, $application], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['partner'], 'authenticated' => true, 'scope' => 'partner:write',
        'api_path' => 'partner/accounts/{partnerApplication}/members', 'http_methods' => ['POST'], 'source_controller' => 'PartnerAccountMemberResource', 'source_action' => 'store'],
    'partner.accounts.members.remove' => ['handler' => $commands, 'method' => 'removeMember', 'parameters' => [$input, $application, $member], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['partner'], 'authenticated' => true, 'scope' => 'partner:write',
        'api_path' => 'partner/accounts/{partnerApplication}/members/{member}', 'http_methods' => ['DELETE'], 'source_controller' => 'PartnerAccountMemberResource', 'source_action' => 'destroy'],
    'partner.accounts.members.index' => ['handler' => $queries, 'method' => 'members', 'parameters' => [$input, $application], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['partner'], 'authenticated' => true, 'scope' => 'partner:read',
        'api_path' => 'partner/accounts/{partnerApplication}/members', 'http_methods' => ['GET'], 'source_controller' => 'PartnerAccountMemberResource', 'source_action' => 'index'],
    'partner.accounts.members.add' => ['handler' => $commands, 'method' => 'addMember', 'parameters' => [$input, $application], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['partner'], 'authenticated' => true, 'scope' => 'partner:write',
        'api_path' => 'partner/accounts/{partnerApplication}/members', 'http_methods' => ['POST'], 'source_controller' => 'PartnerAccountMemberResource', 'source_action' => 'store'],
    'partner.accounts.members.remove' => ['handler' => $commands, 'method' => 'removeMember', 'parameters' => [$input, $application, $member], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['partner'], 'authenticated' => true, 'scope' => 'partner:write',
        'api_path' => 'partner/accounts/{partnerApplication}/members/{member}', 'http_methods' => ['DELETE'], 'source_controller' => 'PartnerAccountMemberResource', 'source_action' => 'destroy'],
    'admin.enquiries.partnerApplications.members.index' => ['handler' => $queries, 'method' => 'members', 'parameters' => [$input, $application], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:read',
        'api_path' => 'crm/partner-applications/{partnerApplication}/members', 'http_methods' => ['GET'], 'source_controller' => 'PartnerApplicationMemberResource', 'source_action' => 'index'],
    'admin.enquiries.partnerApplications.members.eligible' => ['handler' => $queries, 'method' => 'eligibleMembers', 'parameters' => [$input], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:read',
        'api_path' => 'crm/partner-member-options', 'http_methods' => ['GET'], 'source_controller' => 'PartnerApplicationMemberResource', 'source_action' => 'eligible'],
    'admin.enquiries.partnerApplications.members.add' => ['handler' => $commands, 'method' => 'addMember', 'parameters' => [$input, $application], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:write',
        'api_path' => 'crm/partner-applications/{partnerApplication}/members', 'http_methods' => ['POST'], 'source_controller' => 'PartnerApplicationMemberResource', 'source_action' => 'store'],
    'admin.enquiries.partnerApplications.members.remove' => ['handler' => $commands, 'method' => 'removeMember', 'parameters' => [$input, $application, $member], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:write',
        'api_path' => 'crm/partner-applications/{partnerApplication}/members/{member}', 'http_methods' => ['DELETE'], 'source_controller' => 'PartnerApplicationMemberResource', 'source_action' => 'destroy'],
];
