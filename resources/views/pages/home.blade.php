@php $locale = app()->getLocale(); @endphp
<x-layouts.app>
    <section class="relative overflow-hidden bg-gradient-to-br from-navy via-navy-light to-teal-dark">
        <div class="absolute -top-24 -right-24 w-[480px] h-[480px] rounded-full bg-teal-light/10 blur-2xl"></div>

        <div class="relative mx-auto max-w-7xl px-6 py-24 md:py-32">
            <span class="inline-flex items-center gap-2 rounded-full border border-teal-light/40 bg-teal-light/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-teal-light">
                {{ __('home.hero_eyebrow') }}
            </span>

            <h1 class="mt-5 font-display text-4xl md:text-6xl font-extrabold leading-tight text-white max-w-3xl">
                {{ __('home.hero_title') }}
            </h1>

            <p class="mt-5 max-w-xl text-lg text-white/70">
                {{ __('home.hero_tagline') }}
            </p>

            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ url($locale.'/contact') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-gold px-6 py-3 font-display font-bold text-white hover:bg-gold-light">
                    {{ __('home.cta_demo') }}
                </a>
                <a href="{{ url($locale.'/products') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-6 py-3 font-display font-semibold text-white hover:bg-white/10">
                    {{ __('home.cta_explore') }}
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
