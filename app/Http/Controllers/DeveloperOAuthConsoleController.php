<?php

namespace App\Http\Controllers;

use App\Contexts\ApiIdentity\Application\BusinessScopes;
use App\Contexts\ApiIdentity\Application\Queries\DelegatedBusinessUsageQueries;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;

final class DeveloperOAuthConsoleController extends Controller
{
    public function index(Request $request, WorkflowDispatcher $workflows, DelegatedBusinessUsageQueries $usageQueries)
    {
        $result = $workflows->execute('developer.oauth_applications.index', new WorkflowInput(
            values: $request->query(), actor: $request->user(), language: app()->getLocale(),
        ));
        $allowedScopes = BusinessScopes::allowed($request->user());
        $scopeDefinitions = array_intersect_key(BusinessScopes::definitions(), array_flip($allowedScopes));

        $applications = $result->data['items'];
        $clients = Passport::client()->newQuery()
            ->whereIn('id', array_column($applications, 'client_id'))
            ->where('owner_type', $request->user()->getMorphClass())
            ->where('owner_id', $request->user()->getKey())
            ->get()->keyBy(fn ($client) => (string) $client->getKey());
        foreach ($applications as &$application) {
            $client = $clients->get($application['client_id']);
            $application['usage'] = $client
                ? $usageQueries->execute($client, (int) $request->user()->getAuthIdentifier())->data
                : ['requests_today' => 0, 'requests_last_30_days' => 0, 'daily_usage' => []];
        }
        unset($application);

        return view('pages.developers.console', [
            'applications' => $applications,
            'pagination' => $result->data['pagination'],
            'scopeDefinitions' => $scopeDefinitions,
            'machineApplications' => app(WorkflowDispatcher::class)->execute('developer.machine_clients.index', new WorkflowInput(
                values: $request->query(), actor: $request->user(), language: app()->getLocale(),
            ))->data['items'],
        ]);
    }

    public function createMachineClient(Request $request, WorkflowHttpAdapter $workflows)
    {
        return $workflows->webCommand('developer.machine_clients.create', $request, 'developers.console.index');
    }

    public function rotateMachineClient(string $client, Request $request, WorkflowHttpAdapter $workflows)
    {
        return $workflows->webCommand('developer.machine_clients.rotate', $request, 'developers.console.index');
    }

    public function revokeMachineClient(string $client, Request $request, WorkflowHttpAdapter $workflows)
    {
        return $workflows->webCommand('developer.machine_clients.revoke', $request, 'developers.console.index');
    }

    public function create(Request $request, WorkflowHttpAdapter $workflows)
    {
        return $workflows->webCommand('developer.oauth_applications.create', $request, 'developers.console.index');
    }

    public function update(string $client, Request $request, WorkflowHttpAdapter $workflows)
    {
        return $workflows->webCommand('developer.oauth_applications.update', $request, 'developers.console.index');
    }

    public function revoke(string $client, Request $request, WorkflowHttpAdapter $workflows)
    {
        return $workflows->webCommand('developer.oauth_applications.revoke', $request, 'developers.console.index');
    }
}
