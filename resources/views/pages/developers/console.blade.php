@php
    $fr = app()->getLocale() === 'fr';
    $formKey = fn () => (string) \Illuminate\Support\Str::uuid();
    $scopes = $scopeDefinitions;
@endphp
<x-layouts.app
    :title="$fr ? 'Console développeur API | OPES Health Systems' : 'API Developer Console | OPES Health Systems'"
    :description="$fr ? 'Gérez vos applications OAuth et clients API machine OPES.' : 'Manage your OPES OAuth applications and machine API clients.'">
<section class="dev-console" aria-labelledby="developer-console-title">
    <header class="dev-console-hero">
        <p class="dev-console-eyebrow">{{ $fr ? 'Console développeur OPES' : 'OPES Developer Console' }}</p>
        <h1 id="developer-console-title">{{ $fr ? 'Vos applications OAuth' : 'Your OAuth applications' }}</h1>
        <p>{{ $fr ? 'Enregistrez les applications publiques qui se connecteront avec l’autorisation de l’utilisateur. Les identifiants sont réservés à votre compte et les utilisateurs approuvent chaque accès.' : 'Register public applications that connect with user authorization. Applications belong to your account, and people approve each access.' }}</p>
    </header>

    @if(session('status'))
        <div class="dev-console-alert" role="status">{{ session('status') }}</div>
    @endif
    @if(session('developer_oauth_application.client_id'))
        <section class="dev-console-alert dev-console-created" aria-labelledby="created-app-title">
            <h2 id="created-app-title">{{ $fr ? 'Application enregistrée' : 'Application registered' }}</h2>
            <p>{{ $fr ? 'Conservez cet identifiant client. Aucun secret client n’est délivré pour une application publique.' : 'Save this client ID. Public applications are not issued a client secret.' }}</p>
            <code>{{ session('developer_oauth_application.client_id') }}</code>
        </section>
    @endif
    @if(session('developer_machine_client.credentials'))
        @php($issuedMachineClient = session('developer_machine_client.credentials'))
        <section class="dev-console-alert dev-console-created" aria-labelledby="machine-client-secret-title" role="status">
            <h2 id="machine-client-secret-title">{{ $fr ? 'Secret du client API — à enregistrer maintenant' : 'Machine API client secret — save it now' }}</h2>
            <p>{{ $fr ? 'Ce secret est affiché uniquement après sa création ou sa rotation. Stockez-le dans un coffre de secrets côté serveur. Ne le partagez pas et ne le placez jamais dans un navigateur ou une application mobile.' : 'This secret is shown only after creation or rotation. Store it in a server-side secret manager. Do not share it or put it in a browser or mobile app.' }}</p>
            <dl class="dev-console-credential-list">
                <div><dt>{{ $fr ? 'Nom' : 'Name' }}</dt><dd>{{ $issuedMachineClient['name'] }}</dd></div>
                <div><dt>Client ID</dt><dd><code>{{ $issuedMachineClient['client_id'] }}</code></dd></div>
                <div><dt>Client secret</dt><dd><code>{{ $issuedMachineClient['client_secret'] }}</code></dd></div>
                <div><dt>{{ $fr ? 'URL du jeton' : 'Token URL' }}</dt><dd><code>{{ $issuedMachineClient['token_url'] }}</code></dd></div>
                <div><dt>{{ $fr ? 'Portées' : 'Scopes' }}</dt><dd><code>{{ implode(', ', $issuedMachineClient['scopes']) }}</code></dd></div>
            </dl>
        </section>
    @endif

    <section class="dev-console-panel" aria-labelledby="new-app-title">
        <h2 id="new-app-title">{{ $fr ? 'Enregistrer une application' : 'Register an application' }}</h2>
        <p class="dev-console-muted">{{ $fr ? 'Utilisez les URI de redirection HTTPS de production. HTTP est accepté uniquement pour localhost et le développement local. Le flux exige PKCE S256 et n’émet pas de secret.' : 'Use HTTPS redirect URIs for production. HTTP is accepted only for localhost during local development. Authorization requires PKCE S256 and does not issue a client secret.' }}</p>
        @if($scopes)
        <form method="POST" action="{{ route('developers.console.applications.create', ['locale' => app()->getLocale()]) }}" class="dev-console-form">
            @csrf
            <input type="hidden" name="_command_key" value="{{ $formKey() }}">
            <label>{{ $fr ? 'Nom de l’application' : 'Application name' }}
                <input type="text" name="name" value="{{ old('name') }}" minlength="3" maxlength="150" required autocomplete="off">
            </label>
            @error('name')<p class="dev-console-error">{{ $message }}</p>@enderror
            <fieldset class="dev-console-fieldset">
                <legend>{{ $fr ? 'URI de redirection exactes' : 'Exact redirect URIs' }}</legend>
                <div class="redirect-uri-list">
                    @foreach(old('redirect_uris', ['']) as $uri)
                    <label class="redirect-uri-row"><span class="sr-only">{{ $fr ? 'URI de redirection' : 'Redirect URI' }}</span><input type="url" name="redirect_uris[]" value="{{ $uri }}" placeholder="https://app.example.com/oauth/callback" maxlength="255" required></label>
                    @endforeach
                </div>
                <button class="dev-console-text-button add-redirect-uri" type="button">{{ $fr ? 'Ajouter une URI' : 'Add a redirect URI' }}</button>
            </fieldset>
            @error('redirect_uris')<p class="dev-console-error">{{ $message }}</p>@enderror
            @error('redirect_uris.*')<p class="dev-console-error">{{ $message }}</p>@enderror
            <fieldset class="dev-console-fieldset">
                <legend>{{ $fr ? 'Autorisations demandées' : 'Requested scopes' }}</legend>
                <p class="dev-console-muted">{{ $fr ? 'Ne demandez que les accès nécessaires. La disponibilité dépend des rôles de votre compte.' : 'Request only the access you need. Available scopes depend on your account roles.' }}</p>
                <div class="scope-options">
                    @foreach($scopes as $scope => $description)
                    <label class="scope-option"><input type="checkbox" name="scopes[]" value="{{ $scope }}" @checked(in_array($scope, old('scopes', []), true))><span><strong>{{ $scope }}</strong><small>{{ $fr ? 'Accès autorisé selon le rôle' : $description }}</small></span></label>
                    @endforeach
                </div>
            </fieldset>
            @error('scopes')<p class="dev-console-error">{{ $message }}</p>@enderror
            @error('scopes.*')<p class="dev-console-error">{{ $message }}</p>@enderror
            <button type="submit" class="dev-console-submit">{{ $fr ? 'Enregistrer l’application' : 'Register application' }}</button>
        </form>
        @else
        <p class="dev-console-alert">{{ $fr ? 'Aucune portée API n’est actuellement disponible pour les rôles de ce compte.' : 'No API scopes are currently available for this account’s roles.' }}</p>
        @endif
    </section>

    <section class="dev-console-panel" aria-labelledby="existing-apps-title">
        <h2 id="existing-apps-title">{{ $fr ? 'Applications de ce compte' : 'Applications on this account' }}</h2>
        @if($applications)
            @foreach($applications as $application)
            <article class="dev-console-app">
                <div class="dev-console-app-heading">
                    <div><h3>{{ $application['name'] }}</h3><code>{{ $application['client_id'] }}</code></div>
                    <span class="dev-console-status {{ $application['revoked'] ? 'is-revoked' : 'is-active' }}">{{ $application['revoked'] ? ($fr ? 'Révoquée' : 'Revoked') : ($fr ? 'Active' : 'Active') }}</span>
                </div>
                <p class="dev-console-muted">{{ $fr ? 'Portées enregistrées' : 'Registered scopes' }}: {{ $application['scopes'] ? implode(', ', $application['scopes']) : '—' }}</p>
                <ul class="dev-console-uris">
                    @foreach($application['redirect_uris'] as $uri)<li><code>{{ $uri }}</code></li>@endforeach
                </ul>
                <div class="dev-console-usage" aria-label="{{ $fr ? 'Utilisation de cette application' : 'Usage for this application' }}">
                    <div><span>{{ $fr ? 'Requêtes aujourd’hui' : 'Requests today' }}</span><strong>{{ number_format($application['usage']['requests_today']) }}</strong></div>
                    <div><span>{{ $fr ? 'Sur 30 jours' : 'Last 30 days' }}</span><strong>{{ number_format($application['usage']['requests_last_30_days']) }}</strong></div>
                </div>
                <p class="dev-console-muted dev-console-usage-note">{{ $fr ? 'Compteurs limités à votre compte et à cette application OAuth.' : 'Counts are limited to your account and this OAuth application.' }} <code>GET /api/v1/me/usage</code></p>
                <details class="dev-console-usage-detail">
                    <summary>{{ $fr ? 'Voir le détail quotidien sur 30 jours' : 'View daily totals for 30 days' }}</summary>
                    <div class="dev-console-usage-table"><table><thead><tr><th>{{ $fr ? 'Date UTC' : 'UTC date' }}</th><th>{{ $fr ? 'Requêtes' : 'Requests' }}</th></tr></thead><tbody>
                        @foreach($application['usage']['daily_usage'] as $day)<tr><td>{{ $day['date'] }}</td><td>{{ number_format($day['requests']) }}</td></tr>@endforeach
                    </tbody></table></div>
                </details>
                @unless($application['revoked'])
                <details class="dev-console-edit">
                    <summary>{{ $fr ? 'Modifier les paramètres' : 'Edit settings' }}</summary>
                    <form method="POST" action="{{ route('developers.console.applications.update', ['locale' => app()->getLocale(), 'client' => $application['client_id']]) }}" class="dev-console-form">
                        @csrf
                        <input type="hidden" name="_command_key" value="{{ $formKey() }}">
                        <label>{{ $fr ? 'Nom de l’application' : 'Application name' }}<input name="name" value="{{ $application['name'] }}" minlength="3" maxlength="150" required></label>
                        <fieldset class="dev-console-fieldset"><legend>{{ $fr ? 'URI de redirection exactes' : 'Exact redirect URIs' }}</legend><div class="redirect-uri-list">
                            @foreach($application['redirect_uris'] as $uri)<label class="redirect-uri-row"><span class="sr-only">{{ $fr ? 'URI de redirection' : 'Redirect URI' }}</span><input type="url" name="redirect_uris[]" value="{{ $uri }}" maxlength="255" required></label>@endforeach
                        </div><button class="dev-console-text-button add-redirect-uri" type="button">{{ $fr ? 'Ajouter une URI' : 'Add a redirect URI' }}</button></fieldset>
                        <fieldset class="dev-console-fieldset"><legend>{{ $fr ? 'Portées' : 'Scopes' }}</legend><div class="scope-options">
                            @foreach($scopes as $scope => $description)<label class="scope-option"><input type="checkbox" name="scopes[]" value="{{ $scope }}" @checked(in_array($scope, $application['scopes'], true))><span><strong>{{ $scope }}</strong><small>{{ $fr ? 'Accès autorisé selon le rôle' : $description }}</small></span></label>@endforeach
                        </div></fieldset>
                        <button type="submit" class="dev-console-submit">{{ $fr ? 'Enregistrer les modifications' : 'Save changes' }}</button>
                    </form>
                </details>
                <form method="POST" action="{{ route('developers.console.applications.revoke', ['locale' => app()->getLocale(), 'client' => $application['client_id']]) }}" onsubmit="return confirm('{{ $fr ? 'Révoquer cette application et tous ses jetons ?' : 'Revoke this application and all its tokens?' }}')">
                    @csrf<input type="hidden" name="_command_key" value="{{ $formKey() }}">
                    <button type="submit" class="dev-console-revoke">{{ $fr ? 'Révoquer l’application' : 'Revoke application' }}</button>
                </form>
                @endunless
            </article>
            @endforeach
        @else
            <p class="dev-console-muted">{{ $fr ? 'Aucune application OAuth n’est encore associée à ce compte.' : 'No OAuth applications are registered to this account yet.' }}</p>
        @endif
    </section>

    <section class="dev-console-panel" aria-labelledby="machine-clients-title">
        <h2 id="machine-clients-title">{{ $fr ? 'Clients API machine' : 'Machine API clients' }}</h2>
        <p class="dev-console-muted">{{ $fr ? 'Pour les intégrations serveur à serveur. Les identifiants donnent uniquement accès au catalogue public et aux propres compteurs d’utilisation et de quota du client. Aucun accès aux dossiers patients ou aux ressources métier déléguées.' : 'For server-to-server integrations. Credentials only access the public product catalogue and the client’s own usage and quota totals. They cannot access patient records or delegated business resources.' }}</p>
        <p class="dev-console-muted"><code>client_credentials</code> · {{ $fr ? 'maximum cinq clients actifs par compte' : 'up to five active clients per account' }}</p>
        <form method="POST" action="{{ route('developers.console.machine-clients.create', ['locale' => app()->getLocale()]) }}" class="dev-console-form">
            @csrf
            <input type="hidden" name="_command_key" value="{{ $formKey() }}">
            <label>{{ $fr ? 'Nom de l’intégration' : 'Integration name' }}
                <input type="text" name="name" value="{{ old('name') }}" minlength="3" maxlength="100" required autocomplete="off" placeholder="{{ $fr ? 'ex. Synchronisation du catalogue' : 'e.g. Catalogue sync' }}">
            </label>
            @error('name')<p class="dev-console-error">{{ $message }}</p>@enderror
            <fieldset class="dev-console-fieldset">
                <legend>{{ $fr ? 'Accès API' : 'API access' }}</legend>
                <label class="scope-option"><input type="checkbox" name="scopes[]" value="catalogue:read" @checked(in_array('catalogue:read', old('scopes', ['catalogue:read', 'integration:read']), true))><span><strong>catalogue:read</strong><small>{{ $fr ? 'Catalogue public des produits et services.' : 'Public product and service catalogue.' }}</small></span></label>
                <label class="scope-option"><input type="checkbox" name="scopes[]" value="integration:read" @checked(in_array('integration:read', old('scopes', ['catalogue:read', 'integration:read']), true))><span><strong>integration:read</strong><small>{{ $fr ? 'Identité du client, ses propres compteurs et quotas.' : 'Client identity, its own usage and quota totals.' }}</small></span></label>
            </fieldset>
            @error('scopes')<p class="dev-console-error">{{ $message }}</p>@enderror
            @error('scopes.*')<p class="dev-console-error">{{ $message }}</p>@enderror
            <button type="submit" class="dev-console-submit">{{ $fr ? 'Créer un client API machine' : 'Create machine API client' }}</button>
        </form>

        <div class="dev-console-machine-clients">
            @forelse($machineApplications as $machine)
                <article class="dev-console-app">
                    <div class="dev-console-app-heading"><div><h3>{{ $machine['name'] }}</h3><code>{{ $machine['client_id'] }}</code></div>
                        <span class="dev-console-status {{ $machine['revoked'] ? 'is-revoked' : 'is-active' }}">{{ $machine['revoked'] ? ($fr ? 'Révoqué' : 'Revoked') : 'Active' }}</span></div>
                    <p class="dev-console-muted">{{ $fr ? 'Portées' : 'Scopes' }}: <code>{{ implode(', ', $machine['scopes']) ?: '—' }}</code></p>
                    <div class="dev-console-usage"><div><span>{{ $fr ? 'Requêtes aujourd’hui' : 'Requests today' }}</span><strong>{{ number_format($machine['usage']['requests_today']) }}</strong></div>
                        <div><span>{{ $fr ? 'Sur 30 jours' : 'Last 30 days' }}</span><strong>{{ number_format($machine['usage']['requests_last_30_days']) }}</strong></div></div>
                    <p class="dev-console-muted">{{ $fr ? 'Limite quotidienne' : 'Daily limit' }}: {{ $machine['quota']['daily_limit'] === null ? ($fr ? 'non configurée' : 'not configured') : number_format($machine['quota']['daily_limit']) }} · {{ $fr ? 'Limite mensuelle' : 'Monthly limit' }}: {{ $machine['quota']['monthly_limit'] === null ? ($fr ? 'non configurée' : 'not configured') : number_format($machine['quota']['monthly_limit']) }}</p>
                    @unless($machine['revoked'])
                        <form method="POST" action="{{ route('developers.console.machine-clients.rotate', ['locale' => app()->getLocale(), 'client' => $machine['client_id']]) }}" onsubmit="return confirm('{{ $fr ? 'Remplacer le secret et invalider les jetons actuels ?' : 'Replace the secret and invalidate current tokens?' }}')" class="dev-console-inline-form">
                            @csrf<input type="hidden" name="_command_key" value="{{ $formKey() }}"><button type="submit" class="dev-console-revoke">{{ $fr ? 'Faire tourner le secret' : 'Rotate secret' }}</button>
                        </form>
                        <form method="POST" action="{{ route('developers.console.machine-clients.revoke', ['locale' => app()->getLocale(), 'client' => $machine['client_id']]) }}" onsubmit="return confirm('{{ $fr ? 'Révoquer ce client API machine et ses jetons ?' : 'Revoke this machine API client and its tokens?' }}')" class="dev-console-inline-form">
                            @csrf<input type="hidden" name="_command_key" value="{{ $formKey() }}"><button type="submit" class="dev-console-revoke">{{ $fr ? 'Révoquer le client' : 'Revoke client' }}</button>
                        </form>
                    @endunless
                </article>
            @empty
                <p class="dev-console-muted">{{ $fr ? 'Aucun client API machine n’est associé à ce compte.' : 'No machine API clients are registered to this account yet.' }}</p>
            @endforelse
        </div>
    </section>

    <p class="dev-console-security">{{ $fr ? 'Les applications publiques n’ont pas de secret client. Les URI et portées enregistrées sont réservées à votre compte ; toute modification révoque les jetons existants. Une application révoquée ne peut pas être réactivée.' : 'Public applications do not have a client secret. Registered URIs and scopes belong to your account; changes revoke existing tokens. A revoked application cannot be reactivated.' }}</p>
</section>
<style>
.dev-console{max-width:980px;margin:0 auto;padding:clamp(28px,6vw,64px) clamp(16px,3vw,28px);color:var(--text);line-height:1.65}.dev-console-hero{max-width:760px;margin-bottom:28px}.dev-console-eyebrow{color:var(--brand-accent);font-size:14px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.dev-console h1{font-size:clamp(30px,5vw,46px);line-height:1.15;margin:8px 0 14px}.dev-console-hero>p:last-child,.dev-console-muted{color:var(--text-2)}.dev-console-panel{padding:clamp(18px,3vw,28px);margin:20px 0;background:var(--surface);border:1px solid var(--border);border-radius:10px}.dev-console h2{font-size:22px;line-height:1.3;margin:0 0 12px}.dev-console h3{font-size:18px;margin:0 0 5px}.dev-console-form{display:grid;gap:16px;margin-top:18px}.dev-console-form>label{display:grid;gap:6px;font-weight:650}.dev-console input:not([type=checkbox]){width:100%;min-height:44px;padding:10px 12px;border:1px solid var(--control-border);border-radius:5px;background:var(--bg);color:var(--text);font:inherit}.dev-console input:focus-visible,.dev-console button:focus-visible,.dev-console summary:focus-visible{outline:2px solid var(--focus-ring);outline-offset:3px}.dev-console-fieldset{min-width:0;border:1px solid var(--border);border-radius:7px;padding:14px}.dev-console-fieldset legend{font-weight:700;padding:0 5px}.redirect-uri-list,.scope-options{display:grid;gap:9px}.redirect-uri-row{display:block}.dev-console-text-button{margin-top:10px;padding:7px 0;border:0;background:transparent;color:var(--brand-link);font:inherit;text-decoration:underline;cursor:pointer}.scope-option{display:flex;align-items:flex-start;gap:10px;padding:10px;border:1px solid var(--border);border-radius:6px}.scope-option input{margin-top:5px;accent-color:var(--brand-action)}.scope-option span{display:grid;gap:2px}.scope-option small{color:var(--text-2);font-size:13px}.dev-console-submit,.dev-console-revoke{min-height:44px;padding:10px 16px;border:1px solid var(--brand-action);border-radius:5px;font:inherit;font-weight:700;cursor:pointer}.dev-console-submit{justify-self:start;background:var(--brand-action);color:#fff}.dev-console-revoke{margin-top:12px;background:transparent;border-color:var(--border);color:var(--text)}.dev-console-error{margin:-10px 0 0;color:var(--danger,#b42318);font-size:14px}.dev-console-alert{padding:14px 16px;margin:16px 0;background:var(--surface);border:1px solid var(--control-border);border-radius:7px}.dev-console-created code,.dev-console-app code{overflow-wrap:anywhere}.dev-console-created h2{margin-bottom:4px}.dev-console-app{padding:18px 0;border-top:1px solid var(--border)}.dev-console-app-heading{display:flex;gap:14px;align-items:flex-start;justify-content:space-between}.dev-console-status{flex:none;padding:3px 9px;border:1px solid var(--control-border);border-radius:999px;font-size:12px;font-weight:700}.dev-console-status.is-revoked{color:var(--text-2)}.dev-console-uris{display:grid;gap:5px;padding-left:20px}.dev-console-uris code{word-break:break-word}.dev-console-edit{padding-top:8px}.dev-console-edit summary{width:max-content;max-width:100%;color:var(--brand-link);cursor:pointer}.dev-console-security{padding:12px 0;color:var(--text-2);font-size:14px}.dev-console .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
@media(max-width:560px){.dev-console-app-heading{flex-direction:column;gap:8px}.dev-console-status{align-self:flex-start}}
</style>
<style>
.dev-console-usage{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,180px),1fr));gap:10px;max-width:520px;margin:14px 0 6px}.dev-console-usage>div{display:flex;justify-content:space-between;gap:12px;align-items:center;padding:10px 12px;border:1px solid var(--border);border-radius:6px;background:var(--bg)}.dev-console-usage span{color:var(--text-2);font-size:13px}.dev-console-usage strong{font-size:18px;font-variant-numeric:tabular-nums}.dev-console-usage-note{font-size:13px!important}.dev-console-usage-note a{color:var(--brand-link)}
.dev-console-usage-detail{margin:10px 0}.dev-console-usage-detail summary{width:max-content;max-width:100%;color:var(--brand-link);cursor:pointer}.dev-console-usage-table{max-height:300px;overflow:auto;margin-top:8px;border:1px solid var(--border);border-radius:6px}.dev-console-usage-table table{width:100%;border-collapse:collapse}.dev-console-usage-table th,.dev-console-usage-table td{padding:8px 11px;border-bottom:1px solid var(--border);text-align:left;font-size:13px}.dev-console-usage-table th{position:sticky;top:0;background:var(--surface);color:var(--text);text-align:left}
.dev-console-credential-list{display:grid;gap:8px;margin:14px 0 0}.dev-console-credential-list>div{display:grid;grid-template-columns:minmax(120px,180px) minmax(0,1fr);gap:12px}.dev-console-credential-list dt{font-weight:700}.dev-console-credential-list dd{margin:0;overflow-wrap:anywhere}.dev-console-inline-form{display:inline-block;margin-right:8px}.dev-console-machine-clients{margin-top:20px}
</style>
<script>
document.querySelectorAll('.add-redirect-uri').forEach(function(button){button.addEventListener('click',function(){var list=button.parentElement.querySelector('.redirect-uri-list');if(list.children.length>=10)return;var label=document.createElement('label');label.className='redirect-uri-row';var input=document.createElement('input');input.type='url';input.name='redirect_uris[]';input.maxLength=255;input.required=true;input.placeholder='https://app.example.com/oauth/callback';input.setAttribute('aria-label',button.textContent.trim());label.appendChild(input);list.appendChild(label);input.focus();});});
</script>
</x-layouts.app>
