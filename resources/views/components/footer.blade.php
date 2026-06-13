@php $locale = app()->getLocale(); @endphp
<footer class="bg-navy text-white/70 mt-24">
    <div class="mx-auto max-w-7xl px-6 py-14 grid gap-8 md:grid-cols-4">
        <div>
            <div class="font-display font-extrabold text-white text-lg">{{ __('common.company') }}</div>
            <p class="mt-2 text-sm">{{ __('common.tagline_short') }}</p>
            <div class="mt-4"><x-language-switcher /></div>
        </div>
        <div>
            <div class="text-white font-semibold text-sm mb-3">{{ __('nav.products') }}</div>
            <a href="{{ url($locale.'/products') }}" class="block text-sm hover:text-white py-1">{{ __('nav.products') }}</a>
        </div>
        <div>
            <div class="text-white font-semibold text-sm mb-3">{{ __('nav.about') }}</div>
            <a href="{{ url($locale.'/about') }}" class="block text-sm hover:text-white py-1">{{ __('nav.about') }}</a>
            <a href="{{ url($locale.'/blog') }}" class="block text-sm hover:text-white py-1">{{ __('nav.blog') }}</a>
            <a href="{{ url($locale.'/partnerships') }}" class="block text-sm hover:text-white py-1">{{ __('nav.partnerships') }}</a>
        </div>
        <div>
            <div class="text-white font-semibold text-sm mb-3">Douala, Cameroun</div>
            <a href="{{ url($locale.'/contact') }}"
               class="inline-flex bg-gold text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gold-light">
                {{ __('nav.book_demo') }}
            </a>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="mx-auto max-w-7xl px-6 py-5 text-xs">
            © {{ date('Y') }} {{ __('common.company') }} SARL. {{ __('common.all_rights') }}
        </div>
    </div>
</footer>
