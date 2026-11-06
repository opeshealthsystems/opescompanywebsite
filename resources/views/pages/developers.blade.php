@php
    $fr = app()->getLocale() === 'fr';
    $apiContract = json_decode(file_get_contents(base_path('docs/api/openapi.json')), true) ?: [];
    $apiVersion = data_get($apiContract, 'info.version', '');
    $sdkMetadata = json_decode(file_get_contents(public_path('downloads/opes-api-sdk.json')), true) ?: [];
    $sdkVersion = $sdkMetadata['version'] ?? '';
    $sdkUrl = $sdkMetadata['url'] ?? '/downloads/opes-health-api-client.tgz';
@endphp
<x-layouts.app :title="$fr ? 'API et développeurs' : 'APIs and developers'" :description="$fr ? 'Documentation et accès aux API OPES Health Systems.' : 'Documentation and access for OPES Health Systems APIs.'">
<div class="developer-docs">
    <header>
        <p class="dev-label">OPES API · OpenAPI {{ $apiVersion }} · {{ $fr ? 'Aperçu du SDK' : 'SDK preview' }} {{ $sdkVersion }}</p>
        <h1>{{ $fr ? 'Intégrez les API OPES' : 'Build with OPES APIs' }}</h1>
        <p>{{ $fr ? 'Consultez notre catalogue logiciel et transmettez des demandes grâce à des interfaces documentées et versionnées.' : 'Discover our software catalogue and submit enquiries through documented, versioned interfaces.' }}</p>
        <div class="dev-actions">
            <a class="dev-secondary" href="{{ url(app()->getLocale().'/developers/console') }}">{{ $fr ? 'Gérer mes applications et clients API' : 'Manage OAuth apps and API clients' }}</a>
            <a class="dev-secondary" href="{{ url('/api/v1/status') }}">{{ $fr ? 'Statut API' : 'API application status' }}</a>
            <a class="dev-secondary" href="{{ url('/api/v1/api-products') }}">{{ $fr ? 'Catalogue des API' : 'API product catalogue' }}</a>
            <a class="dev-secondary" href="{{ url('/api/v1/changelog') }}">{{ $fr ? 'Journal des versions API' : 'API changelog' }}</a>
            <a href="{{ url('/api/v1/openapi.json') }}">{{ $fr ? 'Référence OpenAPI' : 'OpenAPI reference' }}</a>
            <a class="dev-secondary" href="#integration-request">{{ $fr ? 'Demander un accès API' : 'Request API access' }}</a>
        </div>
    </header>
    <section id="api-products" aria-labelledby="api-products-title">
        <h2 id="api-products-title">{{ $fr ? 'Produits API et limites d’accès' : 'API products and access boundaries' }}</h2>
        <p>{{ $fr ? 'Cette liste est chargée depuis notre API versionnée ; elle décrit les parcours disponibles et leurs règles d’accès actuelles.' : 'This list is loaded from our versioned API and describes current workflows and their access rules.' }}</p>
        <div id="api-product-list" class="api-product-list" aria-live="polite" data-locale="{{ $fr ? 'fr' : 'en' }}">
            <p>{{ $fr ? 'Chargement du catalogue API…' : 'Loading API product catalogue…' }}</p>
        </div>
    </section>
    <div class="dev-grid">
        <section><h2>{{ $fr ? 'Demandes publiques' : 'Public enquiries' }}</h2>
            <p><code>POST /api/v1/enquiries</code></p>
            <p>{{ $fr ? 'Aucun jeton requis. Utilisez une clé Idempotency-Key unique pour éviter les doublons pendant 24 heures. Les demandes sont enregistrées et transmises à notre équipe.' : 'No token required. Use a unique Idempotency-Key to prevent duplicate submissions for 24 hours. Enquiries are recorded and forwarded to our team.' }}</p>
            <p>{{ $fr ? 'Limite : 5 tentatives par minute et 30 par jour, par adresse IP.' : 'Limit: 5 attempts per minute and 30 per day, per IP address.' }}</p>
        </section>
        <section><h2>{{ $fr ? 'Catalogue logiciel' : 'Software catalogue' }}</h2>
            <p><code>GET /api/v1/catalogue/products</code></p>
            <p>{{ $fr ? 'Métadonnées des produits publiés, avec pagination et choix de langue. Nécessite un jeton OAuth 2.0 et la portée catalogue:read.' : 'Published product metadata with pagination and language selection. Requires an OAuth 2.0 token with the catalogue:read scope.' }}</p>
            <p><code>GET /api/v1/integration</code> · <code>integration:read</code></p>
            <p>{{ $fr ? 'Consultez l’identité de votre intégration et son nombre de requêtes authentifiées du jour.' : 'Inspect your integration identity and today’s authenticated request count.' }}</p>
            <p><code>GET /api/v1/integration/usage</code> · <code>integration:read</code></p>
            <p>{{ $fr ? 'Obtenez vos 30 totaux quotidiens UTC. Chaque intégration ne peut lire que ses propres compteurs.' : 'Read your own 30 daily UTC request totals. An integration can only access its own counters.' }}</p>
        </section>
    </div>
    <section><h2>{{ $fr ? 'API métiers et accès utilisateur' : 'Business APIs and user access' }}</h2>
        <p>{{ $fr ? 'Les parcours clients, assistance, formation, praticiens, validation, personnel et comptabilité disposent d’interfaces versionnées. Les actions administratives de projets, licences, contrats, recrutement, validation et finances utilisent également des commandes partagées. Les droits du compte, les portées approuvées et les règles d’accès aux dossiers sont appliqués à chaque appel.' : 'Customer, support, learning, practitioner, validation, workforce, recruitment and accounting workflows have versioned interfaces. Administrative project, licence, contract, recruitment, validation and finance actions also use shared commands. Account permissions, approved scopes and record access rules apply to every call.' }}</p>
        <p>{{ $fr ? 'Les applications approuvées utilisent le code d’autorisation OAuth 2.0 avec PKCE S256, un état aléatoire et le consentement de l’utilisateur. Le flux client_credentials reste réservé au catalogue et à l’identité de l’intégration.' : 'Approved applications use OAuth 2.0 authorization code with S256 PKCE, random state and user consent. The client_credentials grant remains limited to the catalogue and integration identity.' }}</p>
        <p>{{ $fr ? 'Les utilisateurs connectés peuvent enregistrer, consulter, modifier et révoquer leurs propres applications publiques dans la console développeur. La console utilise les mêmes commandes partagées que l’API ; une modification révoque les jetons existants et aucun secret client n’est délivré.' : 'Signed-in users can register, view, update and revoke their own public applications in the Developer Console. The console uses the same shared commands as the API; updates revoke existing tokens, and no client secret is issued.' }} <a href="{{ url($fr ? 'fr/developers/console' : 'en/developers/console') }}">{{ $fr ? 'Ouvrir la console développeur' : 'Open the Developer Console' }}</a></p>
        <h3>{{ $fr ? 'Clients API machine en libre-service' : 'Self-service machine API clients' }}</h3>
        <p>{{ $fr ? 'Les utilisateurs connectés peuvent créer jusqu’à cinq clients machine pour des intégrations serveur à serveur sans attendre l’examen d’une demande d’accès métier. Les portées sont strictement limitées à catalogue:read et integration:read : catalogue public et compteurs d’utilisation/quota du client uniquement. Aucun accès aux dossiers patients ni aux ressources métier déléguées.' : 'Signed-in users can create up to five machine clients for server-to-server integrations without waiting for business API access review. Their scopes are strictly limited to catalogue:read and integration:read: the public catalogue and that client’s own usage/quota data only. They cannot access patient records or delegated business resources.' }}</p>
        <p>{{ $fr ? 'Le secret client est affiché uniquement à la création ou à la rotation. Enregistrez-le dans un gestionnaire de secrets côté serveur. La console et l’API permettent de consulter, faire tourner ou révoquer les clients ; la rotation et la révocation invalident les jetons existants.' : 'The client secret is shown only at creation or rotation. Store it in a server-side secret manager. The console and API support listing, rotating and revoking clients; rotation and revocation invalidate existing tokens.' }} <a href="{{ url($fr ? 'fr/developers/console' : 'en/developers/console') }}">{{ $fr ? 'Gérer les clients API' : 'Manage API clients' }}</a></p>
        <p><code>GET /api/v1/me/usage</code> · <code>identity:read</code></p>
        <p>{{ $fr ? 'Après autorisation utilisateur par PKCE, consultez les 30 totaux quotidiens UTC de l’utilisateur connecté pour cette application OAuth. L’API ne révèle ni l’utilisation d’un autre utilisateur ni celle d’une autre application.' : 'After user authorization with PKCE, read 30 daily UTC request totals for the signed-in user and this OAuth application. The API does not reveal another user’s or another application’s usage.' }}</p>
        <pre><code>GET /oauth/authorize
POST /api/v1/oauth/user-token
GET /api/v1/me/usage
GET /api/v1/customer/tickets
GET /api/v1/me/activity</code></pre>
        <p>{{ $fr ? 'Les commandes métiers nécessitent une clé Idempotency-Key de 16 à 128 caractères. Réutilisez la même clé et le même contenu pendant 24 heures en cas de réponse perdue. Les erreurs utilisent application/problem+json.' : 'Business commands require an Idempotency-Key of 16–128 characters. Reuse the same key and body within 24 hours if a response is lost. Errors use application/problem+json.' }}</p>
    </section>
    <section><h2>{{ $fr ? 'Gestion des integrations administrateur' : 'Administrator integration management' }}</h2>
        <p>{{ $fr ? 'Les administrateurs actifs peuvent consulter et gerer les clients API machine apres examen de la demande : GET et POST /api/v1/admin/api-clients, POST /api/v1/admin/api-clients/{client}/rotate, et DELETE /api/v1/admin/api-clients/{client}. Ces operations exigent un jeton utilisateur OAuth delegue avec la portee api-clients:manage et le role administrateur.' : 'Active administrators can review and manage approved machine API clients through GET and POST /api/v1/admin/api-clients, POST /api/v1/admin/api-clients/{client}/rotate, and DELETE /api/v1/admin/api-clients/{client}. These operations require a delegated user OAuth token with the api-clients:manage scope and an administrator role.' }}</p>
        <p>{{ $fr ? 'Les secrets sont retournes dans une reponse Cache-Control: no-store lors de la creation ou de la rotation; les clients machine restent limites aux portees catalogue:read et integration:read. La documentation OpenAPI contient les schemas et les reponses.' : 'Secrets are returned in a Cache-Control: no-store response when created or rotated; machine clients remain limited to catalogue:read and integration:read. OpenAPI documents the request and response schemas.' }}</p>
        <p><code>GET /api/v1/admin/api-clients/{client}/usage</code> · <code>api-clients:read</code></p>
        <p>{{ $fr ? 'Les administrateurs actifs peuvent consulter un rapport d’assistance sur 30 jours pour un client machine géré. Aucun secret client n’est inclus.' : 'Active administrators can inspect a 30-day support report for one managed machine client. The response never includes a client secret.' }}</p>
        <p><a href="{{ url('/api/v1/openapi.json') }}">{{ $fr ? 'Voir les operations dans OpenAPI' : 'View operations in OpenAPI' }}</a></p>
        <p>{{ $fr ? 'Les applications deleguees examinees peuvent aussi etre listees, enregistrees et revoquees par GET et POST /api/v1/admin/oauth-clients et DELETE /api/v1/admin/oauth-clients/{client}. Elles exigent oauth-clients:manage, un role administrateur, des URI HTTPS exactes ou callback natif OPES et OAuth avec consentement et PKCE S256; aucun secret client n’est emis.' : 'Reviewed delegated applications can also be listed, registered and revoked through GET and POST /api/v1/admin/oauth-clients and DELETE /api/v1/admin/oauth-clients/{client}. They require oauth-clients:manage, an administrator role, exact HTTPS URIs or the reserved OPES Health native callback, user consent and S256 PKCE; no client secret is issued.' }}</p>
    </section>
    <section><h2>{{ $fr ? 'SDK Node.js côté serveur' : 'Server-side Node.js SDK' }}</h2>
        <p>{{ $fr ? 'Le SDK '.$sdkVersion.' fournit les identifiants générés et les métadonnées de toutes les opérations décrites dans le contrat OpenAPI.' : 'SDK '.$sdkVersion.' provides generated operation identifiers and metadata for every operation described by the OpenAPI contract.' }}</p>
        <p>{{ $fr ? 'Client préliminaire côté serveur pour Node.js 22 ou ultérieur, avec identifiants TypeScript générés, exemples d’utilisation et d’assistance, gestion des clés d’idempotence et diagnostics des limites. Il n’est pas encore publié sur npm.' : 'Preview server-side client for Node.js 22 or newer, with generated TypeScript operation IDs, usage and support examples, idempotency-key handling and quota diagnostics. It is not yet published to npm.' }}</p>
        <pre><code>npm install https://opeshealthsystems.com{{ $sdkUrl }}</code></pre>
        <div class="dev-actions">
            <a download href="{{ url($sdkUrl) }}">{{ $fr ? 'Télécharger le SDK (.tgz)' : 'Download SDK (.tgz)' }}</a>
            <a class="dev-secondary" href="{{ url($sdkUrl.'.sha256') }}">{{ $fr ? 'Empreinte SHA-256' : 'SHA-256 checksum' }}</a>
        </div>
    </section>
    <section id="integration-request"><h2>{{ $fr ? 'Demander un accès développeur' : 'Request developer access' }}</h2>
        <p>{{ $fr ? 'Chaque intégration est examinée avant la création des identifiants. N’envoyez jamais de mot de passe, de secret client ni de donnée patient.' : 'Each integration is reviewed before credentials are created. Never submit a password, client secret, or patient information.' }}</p>
        <form id="integration-request-form" class="dev-request-form" data-success="{{ $fr ? 'Votre demande a été enregistrée pour examen.' : 'Your request has been recorded for review.' }}" data-error="{{ $fr ? 'La demande n’a pas pu être envoyée. Réessayez ou contactez notre équipe.' : 'The request could not be submitted. Try again or contact our team.' }}">
            <div class="dev-form-grid">
                <label>{{ $fr ? 'Votre nom' : 'Your name' }}<input name="name" autocomplete="name" maxlength="100" required></label>
                <label>{{ $fr ? 'E-mail professionnel' : 'Work email' }}<input name="email" type="email" autocomplete="email" maxlength="150" required></label>
                <label>{{ $fr ? 'Nom de l’application' : 'Application name' }}<input name="application_name" maxlength="100" required></label>
                <label>{{ $fr ? 'Type d’accès demandé' : 'Requested access type' }}
                    <select name="access_type" required>
                        <option value="machine">{{ $fr ? 'Accès machine' : 'Machine API access' }}</option>
                        <option value="delegated">{{ $fr ? 'API métier déléguée (PKCE)' : 'Delegated business APIs (PKCE)' }}</option>
                        <option value="both">{{ $fr ? 'Les deux accès' : 'Both access types' }}</option>
                    </select>
                </label>
            </div>
            <label>{{ $fr ? 'URI de redirection (HTTPS ou callback OPES, obligatoire pour accès délégué)' : 'Redirect URI (HTTPS or OPES app callback; required for delegated access)' }}<input name="redirect_uri" type="text" maxlength="255" placeholder="https://example.com/callback or opeshealth://oauth/callback"></label>
            <label>{{ $fr ? 'Portées demandées (séparées par des virgules)' : 'Requested scopes (comma separated)' }}<input name="requested_scopes" type="text" maxlength="500" placeholder="catalogue:read, integration:read"></label>
            <label>{{ $fr ? 'Usage prévu' : 'Intended use' }}<textarea name="use_case" rows="4" maxlength="1200" required></textarea></label>
            <button type="submit" class="dev-submit">{{ $fr ? 'Envoyer la demande' : 'Submit access request' }}</button>
            <p id="integration-request-status" class="dev-form-status" role="status" aria-live="polite"></p>
            <noscript><p>Enable JavaScript or email <a href="mailto:contact@opeshealthsystems.com">contact@opeshealthsystems.com</a> to request access.</p></noscript>
        </form>
    </section>
    <section><h2>{{ $fr ? '2. Obtenez un jeton' : '2. Obtain a token' }}</h2>
        <p>{{ $fr ? 'Les intégrations serveur utilisent le flux client_credentials. Les jetons expirent après 15 minutes. Remplacez les valeurs ci-dessous par vos identifiants privés.' : 'Server integrations use the client_credentials grant. Tokens expire after 15 minutes. Replace the placeholders below with your private credentials.' }}</p>
        <pre><code>POST https://opeshealthsystems.com/api/v1/oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=client_credentials&amp;client_id=YOUR_CLIENT_ID&amp;client_secret=YOUR_CLIENT_SECRET&amp;scope=catalogue%3Aread%20integration%3Aread</code></pre>
    </section>
    <section><h2>{{ $fr ? '3. Appelez le catalogue' : '3. Call the catalogue' }}</h2>
        <pre><code>GET https://opeshealthsystems.com/api/v1/catalogue/products?per_page=10&amp;locale={{ $fr ? 'fr' : 'en' }}
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json</code></pre>
        <p>{{ $fr ? 'Limite : 60 appels authentifiés par minute et par client. Respectez Retry-After en cas de réponse 429. Fournissez le X-Request-ID à notre support pour une investigation.' : 'Limit: 60 authenticated calls per minute per client. Respect Retry-After on a 429 response. Share X-Request-ID with support when investigating a request.' }}</p>
    </section>
    <section><h2>{{ $fr ? 'Disponibilité et assistance' : 'Availability and support' }}</h2>
        <p>{{ $fr ? 'Les API sont en version préliminaire. Aucun environnement sandbox public ni engagement de disponibilité n’est actuellement annoncé. L’accès machine au catalogue et aux compteurs d’intégration est libre-service et limité aux portées indiquées ci-dessus. L’accès aux API métier déléguées nécessite une application publique PKCE, un consentement utilisateur et les portées appropriées ; certains accès sont examinés avant provisionnement. Ces interfaces ne constituent pas une API de dossier médical.' : 'The APIs are in preview. A public sandbox and service availability commitment are not currently offered. Machine access to the catalogue and integration usage is self-service and restricted to the scopes described above. Delegated business API access requires a public PKCE application, user consent and appropriate scopes; some access requests are reviewed before provisioning. These interfaces do not constitute an electronic medical record API.' }}</p>
        <p><a href="mailto:contact@opeshealthsystems.com">contact@opeshealthsystems.com</a> · <a href="{{ url('/api/v1/openapi.json') }}">{{ $fr ? 'Contrat et réponses détaillées' : 'Contract and detailed responses' }}</a></p>
    </section>
</div>
<style>
.developer-docs{max-width:1080px;margin:0 auto;padding:64px 24px;color:var(--text);line-height:1.7}
.developer-docs header{margin-bottom:40px}.developer-docs h1{font-size:clamp(32px,5vw,48px);line-height:1.15;margin:12px 0 20px}.developer-docs h2{font-size:24px;line-height:1.3;margin:0 0 16px}.developer-docs p{margin:0 0 16px;font-size:17px;color:var(--text-2)}
.developer-docs .dev-label{color:var(--brand-accent);font-size:14px}.developer-docs section{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:28px;margin-bottom:24px;min-width:0}.dev-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.developer-docs code{font-size:14px;overflow-wrap:anywhere}.developer-docs pre{padding:20px;border:1px solid var(--border);border-radius:4px;background:var(--bg);white-space:pre-wrap;overflow-wrap:anywhere;margin-bottom:20px}.developer-docs a{color:var(--brand-link);text-decoration:underline}.dev-actions{display:flex;gap:16px;flex-wrap:wrap;margin-top:24px}.developer-docs .dev-actions a{display:inline-flex;align-items:center;min-height:44px;padding:10px 20px;border-radius:4px;background:var(--brand-action);color:#fff;text-decoration:none;border:1px solid var(--brand-action)}.developer-docs .dev-actions .dev-secondary{background:var(--surface);border-color:var(--control-border);color:var(--text)}.developer-docs a:focus-visible{outline:2px solid var(--focus-ring);outline-offset:4px}
.dev-request-form{display:grid;gap:16px}.dev-request-form label{display:grid;gap:6px;font-size:15px;font-weight:600;color:var(--text)}.dev-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.dev-request-form input,.dev-request-form select,.dev-request-form textarea{width:100%;min-height:44px;padding:10px 12px;border:1px solid var(--control-border);border-radius:4px;background:var(--bg);color:var(--text);font:inherit}.dev-request-form textarea{min-height:120px;resize:vertical}.dev-request-form input:focus-visible,.dev-request-form select:focus-visible,.dev-request-form textarea:focus-visible,.dev-submit:focus-visible{outline:2px solid var(--focus-ring);outline-offset:2px}.dev-submit{justify-self:start;min-height:44px;padding:10px 20px;border:1px solid var(--brand-action);border-radius:4px;background:var(--brand-action);color:#fff;font:inherit;font-weight:600;cursor:pointer}.dev-submit:disabled{opacity:.65;cursor:wait}.developer-docs .dev-form-status{min-height:1.5em;margin:0;font-size:15px}
.api-product-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr));gap:16px}.api-product-card{min-width:0;padding:20px;border:1px solid var(--border);border-radius:6px;background:var(--bg)}.api-product-card h3{margin:8px 0;font-size:19px;line-height:1.35}.api-product-card p{font-size:15px;overflow-wrap:anywhere}.api-product-card .api-product-meta{font-size:13px;font-weight:700;color:var(--brand-accent);text-transform:uppercase;letter-spacing:.04em}
@media(max-width:560px){.dev-form-grid{grid-template-columns:1fr}}@media(max-width:720px){.dev-grid,.dev-form-grid{grid-template-columns:1fr}.dev-grid{gap:0}.developer-docs{padding:40px 16px}.developer-docs section{padding:20px}}
</style>
<script>
(() => {
    const productList = document.getElementById('api-product-list');
    if (productList) {
        fetch('/api/v1/api-products', { headers: { Accept: 'application/json', 'Accept-Language': productList.dataset.locale || 'en' }, credentials: 'omit' })
            .then(response => { if (!response.ok) throw new Error('catalogue-unavailable'); return response.json(); })
            .then(payload => {
                const items = payload?.data?.items;
                if (!Array.isArray(items) || items.length === 0) throw new Error('catalogue-empty');
                productList.replaceChildren(...items.map(item => {
                    const card = document.createElement('article'); card.className = 'api-product-card';
                    const meta = document.createElement('p'); meta.className = 'api-product-meta';
                    meta.textContent = `${item.availability} · ${item.authentication}${item.scope ? ` · ${item.scope}` : ''}`;
                    const title = document.createElement('h3'); title.textContent = item.name;
                    const summary = document.createElement('p'); summary.textContent = item.summary;
                    const access = document.createElement('p'); access.textContent = item.access;
                    const boundary = document.createElement('p'); boundary.textContent = item.data_boundary;
                    card.append(meta, title, summary, access, boundary);
                    return card;
                }));
            })
            .catch(() => { productList.textContent = productList.dataset.locale === 'fr' ? 'Le catalogue API est temporairement indisponible. Consultez le contrat OpenAPI.' : 'The API catalogue is temporarily unavailable. View the OpenAPI contract.'; });
    }
    const form = document.getElementById('integration-request-form');
    const status = document.getElementById('integration-request-status');
    if (!form || !status) return;
    const accessSelect = form.elements.namedItem('access_type');
    const callbackInput = form.elements.namedItem('redirect_uri');
    const syncCallbackRequirement = () => { callbackInput.required = accessSelect.value !== 'machine'; };
    accessSelect.addEventListener('change', syncCallbackRequirement);
    syncCallbackRequirement();
    const createKey = () => Array.from(crypto.getRandomValues(new Uint8Array(16)), byte => byte.toString(16).padStart(2, '0')).join('');
    let idempotencyKey = createKey();
    form.addEventListener('input', () => { idempotencyKey = createKey(); });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        const fields = new FormData(form);
        const accessType = String(fields.get('access_type')) || 'machine';
        const callback = String(fields.get('redirect_uri') || '').trim();
        const requestedScopes = String(fields.get('requested_scopes') || '').split(/[\s,]+/).map(scope => scope.trim()).filter(Boolean);
        const intendedUse = String(fields.get('use_case')).trim();
        const payload = {
            name: String(fields.get('name')).trim(),
            email: String(fields.get('email')).trim(),
            facility_type: 'API integration',
            message: intendedUse,
            integration_application: {
                application_name: String(fields.get('application_name')).trim(),
                access_type: accessType,
                redirect_uri: callback || null,
                requested_scopes: requestedScopes,
                use_case: intendedUse,
            },
            locale: @json(app()->getLocale()),
        };
        button.disabled = true;
        status.textContent = '';
        try {
            const response = await fetch('/api/v1/enquiries', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Idempotency-Key': idempotencyKey },
                body: JSON.stringify(payload),
                credentials: 'omit',
            });
            if (!response.ok) throw new Error('request-rejected');
            status.textContent = form.dataset.success;
            form.reset();
            idempotencyKey = createKey();
        } catch {
            status.textContent = form.dataset.error;
        } finally {
            button.disabled = false;
        }
    });
})();
</script></x-layouts.app>

