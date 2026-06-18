@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app>

<div class="cs-wrap">
    <div class="cs-icon">
        <i data-lucide="construction" style="width:32px;height:32px;color:#00C896"></i>
    </div>
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="clock" style="width:11px;height:11px"></i>
        {{ $isFr ? 'Bientôt disponible' : 'Coming Soon' }}
    </div>
    <h1 class="cs-title">{{ $page }}</h1>
    <p class="cs-sub">{{ $isFr ? 'Cette section est en cours de construction. Revenez bientôt — ou contactez-nous directement pour en savoir plus sur OPES Health Systems.' : 'This section is under construction. Check back soon — or contact us directly to learn more about OPES Health Systems.' }}</p>
    <div class="cs-actions">
        <a href="{{ url($locale) }}" class="btn-secondary">
            <i data-lucide="arrow-left" style="width:14px;height:14px;color:#94a3b8"></i>
            {{ $isFr ? 'Retour à l\'accueil' : 'Back to Home' }}
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Contactez-nous' : 'Contact Us' }}
            <i data-lucide="arrow-right" style="width:14px;height:14px"></i>
        </a>
    </div>
</div>

</x-layouts.app>
