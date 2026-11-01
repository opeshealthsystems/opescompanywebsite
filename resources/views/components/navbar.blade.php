@php $locale = app()->getLocale(); @endphp

<nav class="site-nav" id="siteNav">
    <a href="{{ url($locale) }}" class="nav-logo">
        <img class="site-seal" src="{{ asset('images/opes-seal.png') }}" alt="" width="44" height="44" style="object-fit:contain;flex-shrink:0">
        <div class="nav-logo-text">
            <div class="nav-logo-name">OPES <span>Health</span> Systems</div>
            <div class="nav-logo-tagline">{{ $locale === 'fr' ? 'Infrastructure de santé numérique pour l\'Afrique' : 'Digital Health Infrastructure for Africa' }}</div>
        </div>
    </a>

    <button class="nav-toggle" type="button" aria-label="{{ $locale === 'fr' ? 'Afficher le menu' : 'Toggle menu' }}" aria-controls="site-navigation" aria-expanded="false">
        <i data-lucide="menu" class="nav-toggle-open" style="width:22px;height:22px"></i>
        <i data-lucide="x" class="nav-toggle-close" style="width:22px;height:22px"></i>
    </button>

    <div class="nav-collapse" id="site-navigation">
    <div class="nav-links">
        @foreach(['products' => ['Products','Produits'], 'solutions' => ['Solutions','Solutions'], 'mobile-app' => ['Mobile app','Application mobile'], 'about' => ['Company','Entreprise'], 'blog' => ['Resources','Ressources'], 'compliance' => ['Trust','Confiance'], 'contact' => ['Contact','Contact']] as $path => $labels)
        <a href="{{ url($locale.'/'.$path) }}">{{ $labels[$locale === 'fr' ? 1 : 0] }}</a>
        @endforeach
    </div>

    <div class="nav-right">
        <x-language-switcher />

        @auth
        @php
            $acctUser = auth()->user();
            $acctPortal = collect(['customer','practitioner','tester','manager','hr','accountant','support'])
                ->first(fn ($r) => $acctUser->hasRole($r));
            $acctIsAdmin = $acctUser->hasAnyRole(['admin','super_admin']);
        @endphp
        {{-- Logged-in account menu --}}
        <div class="nav-account">
            <button class="nav-account-avatar" aria-label="Account menu" aria-haspopup="menu" aria-expanded="false" type="button">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </button>
            <div class="nav-account-drop">
                <div class="nav-account-drop-header">
                    <div class="nav-account-drop-name">{{ auth()->user()->name }}</div>
                    <div class="nav-account-drop-email">{{ auth()->user()->email }}</div>
                </div>
                <div class="nav-account-drop-divider"></div>
                @if($acctIsAdmin)
                <a href="{{ url('admin') }}" class="nav-account-drop-item">
                    <i data-lucide="layout-dashboard" style="width:14px;height:14px"></i> {{ $locale === 'fr' ? 'Console admin' : 'Admin panel' }}
                </a>
                @elseif($acctPortal)
                <a href="{{ route($acctPortal.'.dashboard', ['locale' => $locale]) }}" class="nav-account-drop-item">
                    <i data-lucide="layout-dashboard" style="width:14px;height:14px"></i> {{ $locale === 'fr' ? 'Tableau de bord' : 'Dashboard' }}
                </a>
                <a href="{{ route($acctPortal.'.profile', ['locale' => $locale]) }}" class="nav-account-drop-item">
                    <i data-lucide="user" style="width:14px;height:14px"></i> {{ $locale === 'fr' ? 'Profil' : 'Profile' }}
                </a>
                @if($acctPortal === 'customer')
                <a href="{{ route('customer.licenses', ['locale' => $locale]) }}" class="nav-account-drop-item">
                    <i data-lucide="key" style="width:14px;height:14px"></i> {{ $locale === 'fr' ? 'Licences' : 'Licenses' }}
                </a>
                <a href="{{ route('customer.tickets', ['locale' => $locale]) }}" class="nav-account-drop-item">
                    <i data-lucide="ticket" style="width:14px;height:14px"></i> Support
                </a>
                @endif
                @endif
                <div class="nav-account-drop-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-account-drop-item nav-account-drop-logout">
                        <i data-lucide="log-out" style="width:14px;height:14px"></i> {{ $locale === 'fr' ? 'Déconnexion' : 'Sign out' }}
                    </button>
                </form>
            </div>
        </div>
        @else
        {{-- Guest login link --}}
        <a href="{{ route('login') }}" class="nav-account-login" title="{{ $locale === 'fr' ? 'Connexion' : 'Sign in' }}">
            <i data-lucide="user-circle" style="width:20px;height:20px"></i>
        </a>
        @endauth

        <a href="{{ url($locale.'/book-demo') }}" class="btn-cta">
            {{ __('nav.book_demo') }}
        </a>
    </div>
    </div>
</nav>

<script>
(function () {
    var nav = document.getElementById('siteNav');
    if (!nav) return;
    nav.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        nav.classList.remove('nav-open');
        nav.querySelector('.nav-toggle').setAttribute('aria-expanded', 'false');
        nav.querySelectorAll('.open').forEach(function (el) { el.classList.remove('open'); });
        nav.querySelectorAll('[aria-expanded]').forEach(function (el) { el.setAttribute('aria-expanded', 'false'); });
        nav.querySelector('.nav-logo').focus();
    });

    // Mobile drawer (hamburger).
    var toggle = nav.querySelector('.nav-toggle');
    if (toggle) {
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('nav-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        // Close the drawer after tapping a link.
        nav.querySelectorAll('.nav-collapse a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('nav-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // Account menu — tap/click operable (touch has no hover) + state announced.
    var account = nav.querySelector('.nav-account');
    var avatar = account && account.querySelector('.nav-account-avatar');
    if (account && avatar) {
        var setAccount = function (open) {
            account.classList.toggle('open', open);
            avatar.setAttribute('aria-expanded', open ? 'true' : 'false');
        };
        avatar.addEventListener('click', function (e) {
            e.stopPropagation();
            setAccount(!account.classList.contains('open'));
        });
        document.addEventListener('click', function (e) {
            if (!account.contains(e.target)) setAccount(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setAccount(false);
        });
    }

    // Language switcher — click the globe to open/close the locale dropdown.
    var lang = nav.querySelector('.lang-switcher');
    var langTrigger = lang && lang.querySelector('.lang-trigger');
    if (lang && langTrigger) {
        var setLang = function (open) {
            lang.classList.toggle('open', open);
            langTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        };
        langTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            setLang(!lang.classList.contains('open'));
        });
        document.addEventListener('click', function (e) {
            if (!lang.contains(e.target)) setLang(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setLang(false);
        });
    }

    // Mega-menu triggers: keep aria-expanded in sync with the hover/focus reveal.
    nav.querySelectorAll('.nav-dropdown-wrap').forEach(function (wrap) {
        var trigger = wrap.querySelector('.nav-dropdown-trigger');
        if (!trigger) return;
        var setState = function (open) { trigger.setAttribute('aria-expanded', open ? 'true' : 'false'); };
        wrap.addEventListener('mouseenter', function () { setState(true); });
        wrap.addEventListener('mouseleave', function () { setState(false); });
        wrap.addEventListener('focusin', function () { setState(true); });
        wrap.addEventListener('focusout', function () {
            if (!wrap.contains(document.activeElement)) setState(false);
        });
    });
})();
</script>
