@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; $answers = array_map(fn ($item) => $item[$locale], config('public_faq')); @endphp
<x-layouts.app>
<section class="section" style="max-width:900px;margin:auto">
    <h1 class="section-title">{{ $isFr ? 'Questions et réponses sur OPES' : 'Questions and answers about OPES' }}</h1>
    <p class="section-sub">{{ $isFr ? 'Des réponses directes pour préparer votre projet de logiciel de santé.' : 'Direct answers to help plan your healthcare software project.' }}</p>
    @foreach($answers as [$question, $answer])
    <section style="margin:28px 0">
        <h2 style="font-size:21px;color:var(--text);margin-bottom:10px">{{ $question }}</h2>
        <p style="color:var(--text-2);line-height:1.8">{{ $answer }}</p>
    </section>
    @endforeach
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:32px"><a class="btn-primary" href="{{ route('book-demo') }}">{{ $isFr ? 'Demander une démonstration' : 'Request a demonstration' }}</a><a class="btn-secondary" href="{{ route('contact') }}">{{ $isFr ? 'Contacter notre équipe' : 'Contact our team' }}</a></div>
</section>
@push('schema')
<script type="application/ld+json"><?php echo json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'inLanguage' => $locale, 'mainEntity' => array_map(fn ($pair) => ['@type' => 'Question', 'name' => $pair[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $pair[1]]], $answers)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?></script>
@endpush
</x-layouts.app>
