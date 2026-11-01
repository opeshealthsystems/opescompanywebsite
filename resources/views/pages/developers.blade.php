@php $fr = app()->getLocale() === 'fr'; @endphp
<x-layouts.app :title="$fr ? 'API et développeurs' : 'APIs and developers'" :description="$fr ? 'Documentation et accès aux API OPES Health Systems.' : 'Documentation and access for OPES Health Systems APIs.'">
<div class="developer-docs">
    <header>
        <p class="dev-label">OPES API · {{ $fr ? 'Version préliminaire' : 'Preview' }} 0.23.0</p>
        <h1>{{ $fr ? 'Intégrez les API OPES' : 'Build with OPES APIs' }}</h1>
        <p>{{ $fr ? 'Consultez notre catalogue logiciel et transmettez des demandes grâce à des interfaces documentées et versionnées.' : 'Discover our software catalogue and submit enquiries through documented, versioned interfaces.' }}</p>
        <div class="dev-actions">
            <a href="{{ url('/api/v1/openapi.json') }}">{{ $fr ? 'Référence OpenAPI' : 'OpenAPI reference' }}</a>
            <a class="dev-secondary" href="{{ url(app()->getLocale().'/contact') }}">{{ $fr ? 'Demander un accès' : 'Request integration access' }}</a>
        </div>
    </header>
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
        </section>
    </div>
    <section><h2>{{ $fr ? 'API métiers et accès utilisateur' : 'Business APIs and user access' }}</h2>
        <p>{{ $fr ? 'Les parcours clients, assistance, formation, praticiens, validation, personnel et comptabilité disposent d’interfaces versionnées. Les actions administratives de projets, licences, contrats, recrutement, validation et finances utilisent également des commandes partagées. Les droits du compte, les portées approuvées et les règles d’accès aux dossiers sont appliqués à chaque appel.' : 'Customer, support, learning, practitioner, validation, workforce, recruitment and accounting workflows have versioned interfaces. Administrative project, licence, contract, recruitment, validation and finance actions also use shared commands. Account permissions, approved scopes and record access rules apply to every call.' }}</p>
        <p>{{ $fr ? 'Les applications approuvées utilisent le code d’autorisation OAuth 2.0 avec PKCE S256, un état aléatoire et le consentement de l’utilisateur. Le flux client_credentials reste réservé au catalogue et à l’identité de l’intégration.' : 'Approved applications use OAuth 2.0 authorization code with S256 PKCE, random state and user consent. The client_credentials grant remains limited to the catalogue and integration identity.' }}</p>
        <pre><code>GET /oauth/authorize
POST /api/v1/oauth/user-token
GET /api/v1/customer/tickets
GET /api/v1/me/activity</code></pre>
        <p>{{ $fr ? 'Les commandes métiers nécessitent une clé Idempotency-Key de 16 à 128 caractères. Réutilisez la même clé et le même contenu pendant 24 heures en cas de réponse perdue. Les erreurs utilisent application/problem+json.' : 'Business commands require an Idempotency-Key of 16–128 characters. Reuse the same key and body within 24 hours if a response is lost. Errors use application/problem+json.' }}</p>
    </section>
    <section><h2>{{ $fr ? 'SDK Node.js côté serveur' : 'Server-side Node.js SDK' }}</h2>
        <p>{{ $fr ? 'Le SDK 0.24.0 couvre le catalogue, l’identité machine, les demandes publiques et les opérations métiers déléguées définies dans le contrat OpenAPI.' : 'SDK 0.24.0 covers the catalogue, machine identity, public enquiries and delegated business operations described by the OpenAPI contract.' }}</p>
        <p>{{ $fr ? 'Client préliminaire côté serveur pour Node.js 22 ou ultérieur, avec identifiants TypeScript générés, gestion des clés d’idempotence et diagnostics des limites.' : 'Preview server-side client for Node.js 22 or newer, with generated TypeScript operation IDs, idempotency-key handling and quota diagnostics.' }}</p>
        <pre><code>npm install https://opeshealthsystems.com/downloads/opes-health-api-client-0.24.0.tgz</code></pre>
        <div class="dev-actions">
            <a download href="{{ url('/downloads/opes-health-api-client-0.24.0.tgz') }}">{{ $fr ? 'Télécharger le SDK (.tgz)' : 'Download SDK (.tgz)' }}</a>
            <a class="dev-secondary" href="{{ url('/downloads/opes-health-api-client-0.24.0.tgz.sha256') }}">{{ $fr ? 'Empreinte SHA-256' : 'SHA-256 checksum' }}</a>
        </div>
    </section>
    <section><h2>{{ $fr ? '1. Demandez vos identifiants' : '1. Request credentials' }}</h2>
        <p>{{ $fr ? 'Indiquez à notre équipe le nom de votre application, son usage et les portées souhaitées. Les accès sont accordés après examen. Conservez le secret côté serveur ; ne l’intégrez jamais dans une application navigateur ou mobile.' : 'Tell our team your application name, intended use and required scopes. Access is issued after review. Keep the client secret on your server; never embed it in browser or mobile applications.' }}</p>
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
        <p>{{ $fr ? 'Ces API sont en version préliminaire. Aucun environnement sandbox public ni engagement de disponibilité n’est annoncé. OpenID Connect, l’inscription autonome des intégrations et la migration de toutes les actions administratives restent à réaliser. Ces interfaces ne représentent pas une API de dossier médical.' : 'These APIs are in preview. A public sandbox and service availability commitment are not yet offered. OpenID Connect, self-service application registration and migration of all administrative actions remain outstanding. These interfaces do not constitute an electronic medical record API.' }}</p>
        <p><a href="mailto:contact@opeshealthsystems.com">contact@opeshealthsystems.com</a> · <a href="{{ url('/api/v1/openapi.json') }}">{{ $fr ? 'Contrat et réponses détaillées' : 'Contract and detailed responses' }}</a></p>
    </section>
</div>
<style>
.developer-docs{max-width:1080px;margin:0 auto;padding:64px 24px;color:var(--text);line-height:1.7}
.developer-docs header{margin-bottom:40px}.developer-docs h1{font-size:clamp(32px,5vw,48px);line-height:1.15;margin:12px 0 20px}.developer-docs h2{font-size:24px;line-height:1.3;margin:0 0 16px}.developer-docs p{margin:0 0 16px;font-size:17px;color:var(--text-2)}
.developer-docs .dev-label{color:var(--brand-accent);font-size:14px}.developer-docs section{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:28px;margin-bottom:24px;min-width:0}.dev-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.developer-docs code{font-size:14px;overflow-wrap:anywhere}.developer-docs pre{padding:20px;border:1px solid var(--border);border-radius:4px;background:var(--bg);white-space:pre-wrap;overflow-wrap:anywhere;margin-bottom:20px}.developer-docs a{color:var(--brand-link);text-decoration:underline}.dev-actions{display:flex;gap:16px;flex-wrap:wrap;margin-top:24px}.developer-docs .dev-actions a{display:inline-flex;align-items:center;min-height:44px;padding:10px 20px;border-radius:4px;background:var(--brand-action);color:#fff;text-decoration:none;border:1px solid var(--brand-action)}.developer-docs .dev-actions .dev-secondary{background:var(--surface);border-color:var(--control-border);color:var(--text)}.developer-docs a:focus-visible{outline:2px solid var(--focus-ring);outline-offset:4px}
@media(max-width:720px){.dev-grid{grid-template-columns:1fr;gap:0}.developer-docs{padding:40px 16px}.developer-docs section{padding:20px}}
</style>
</x-layouts.app>

