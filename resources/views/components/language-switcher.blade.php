@php
    $segments = request()->segments();
    if (empty($segments)) { $segments = [app()->getLocale()]; }
    $current = app()->getLocale();
    $names = ['en' => 'English', 'fr' => 'Français'];
    $langs = [];
    foreach (config('locale.supported') as $code) {
        $seg = $segments; $seg[0] = $code;
        $langs[$code] = ['label' => $names[$code] ?? strtoupper($code), 'url' => url(implode('/', $seg))];
    }
@endphp

<div class="lang-switcher">
    <button type="button" class="lang-trigger" aria-haspopup="menu" aria-expanded="false"
            aria-label="{{ $current === 'fr' ? 'Choisir la langue' : 'Select language' }}">
        <i data-lucide="globe" style="width:15px;height:15px"></i>
        <span class="lang-current">{{ strtoupper($current) }}</span>
        <i data-lucide="chevron-down" aria-hidden="true" class="lang-caret" style="width:12px;height:12px"></i>
    </button>
    <div class="lang-menu" role="menu">
        @foreach($langs as $code => $lang)
        <a href="{{ $lang['url'] }}" role="menuitem"
           class="lang-option{{ $current === $code ? ' is-active' : '' }}"
           @if($current === $code) aria-current="true" @endif>
            <span class="lang-option-code">{{ strtoupper($code) }}</span>
            <span class="lang-option-label">{{ $lang['label'] }}</span>
        </a>
        @endforeach
    </div>
</div>
