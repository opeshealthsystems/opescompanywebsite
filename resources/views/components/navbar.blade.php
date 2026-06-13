@php $locale = app()->getLocale(); @endphp

<nav class="site-nav">
    <a href="{{ url($locale) }}" class="nav-logo">
        <div class="nav-logo-mark">O</div>
        <div class="nav-logo-text">Opes <span>Health</span> Systems</div>
    </a>

    <div class="nav-links">
        <a href="{{ url($locale.'/products') }}">
            {{ __('nav.products') }}
            <i data-lucide="chevron-down" style="width:13px;height:13px;opacity:.5"></i>
        </a>
        <a href="{{ url($locale.'/solutions') }}">
            {{ __('nav.solutions') }}
            <i data-lucide="chevron-down" style="width:13px;height:13px;opacity:.5"></i>
        </a>
        <a href="{{ url($locale.'/about') }}">{{ __('nav.about') }}</a>
        <a href="{{ url($locale.'/partnerships') }}">{{ __('nav.partnerships') }}</a>
        <a href="{{ url($locale.'/blog') }}">{{ __('nav.blog') }}</a>
        <a href="{{ url($locale.'/contact') }}">{{ __('nav.contact') }}</a>
    </div>

    <div class="nav-right">
        <x-language-switcher />
        <a href="{{ url($locale.'/contact') }}" class="btn-cta">
            {{ __('nav.book_demo') }}
        </a>
    </div>
</nav>
