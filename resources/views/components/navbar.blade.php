@php $locale = app()->getLocale(); @endphp
<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-6 h-16 flex items-center justify-between">
        <a href="{{ url($locale) }}" class="font-display font-extrabold text-lg text-teal">
            {{ __('common.company') }}
        </a>

        <div class="hidden md:flex items-center gap-7 text-sm font-medium text-muted">
            <a href="{{ url($locale.'/products') }}" class="hover:text-teal">{{ __('nav.products') }}</a>
            <a href="{{ url($locale.'/solutions') }}" class="hover:text-teal">{{ __('nav.solutions') }}</a>
            <a href="{{ url($locale.'/partnerships') }}" class="hover:text-teal">{{ __('nav.partnerships') }}</a>
            <a href="{{ url($locale.'/blog') }}" class="hover:text-teal">{{ __('nav.blog') }}</a>
            <a href="{{ url($locale.'/about') }}" class="hover:text-teal">{{ __('nav.about') }}</a>
        </div>

        <div class="flex items-center gap-4">
            <x-language-switcher />
            <a href="{{ url($locale.'/contact') }}"
               class="hidden sm:inline-flex bg-teal text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-teal-dark">
                {{ __('nav.book_demo') }}
            </a>
        </div>
    </div>
</nav>
