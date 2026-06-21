@php
    $segments = request()->segments();
    if (empty($segments)) { $segments = [app()->getLocale()]; }
    $toEn = $segments; $toEn[0] = 'en';
    $toFr = $segments; $toFr[0] = 'fr';
    $current = app()->getLocale();
@endphp

<div class="lang-switcher">
    <i data-lucide="globe" style="width:13px;height:13px;color:var(--text-muted)"></i>
    <a href="{{ url(implode('/', $toEn)) }}" class="{{ $current === 'en' ? 'lang-active' : '' }}">EN</a>
    <span class="lang-sep">·</span>
    <a href="{{ url(implode('/', $toFr)) }}" class="{{ $current === 'fr' ? 'lang-active' : '' }}">FR</a>
</div>
