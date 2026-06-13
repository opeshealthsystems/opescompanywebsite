@php
    $segments = request()->segments();
    if (empty($segments)) { $segments = [app()->getLocale()]; }
    $toEn = $segments; $toEn[0] = 'en';
    $toFr = $segments; $toFr[0] = 'fr';
    $current = app()->getLocale();
@endphp

<div class="flex items-center gap-1 text-sm font-semibold">
    <a href="{{ url(implode('/', $toEn)) }}"
       class="px-2 py-1 rounded {{ $current === 'en' ? 'bg-teal text-white' : 'text-muted hover:text-teal' }}">EN</a>
    <span class="text-muted">/</span>
    <a href="{{ url(implode('/', $toFr)) }}"
       class="px-2 py-1 rounded {{ $current === 'fr' ? 'bg-teal text-white' : 'text-muted hover:text-teal' }}">FR</a>
</div>
