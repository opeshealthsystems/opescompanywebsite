@props(['paginator'])
@if($paginator->hasPages())
@php $isFr = app()->getLocale() === 'fr'; @endphp
<nav aria-label="{{ $isFr ? 'Pagination des articles' : 'Article pagination' }}" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;color:var(--text-2)">
    @if($paginator->previousPageUrl())
        <a class="btn-secondary" rel="prev" href="{{ $paginator->previousPageUrl() }}">{{ $isFr ? 'Précédent' : 'Previous' }}</a>
    @else
        <span>{{ $isFr ? 'Première page' : 'First page' }}</span>
    @endif
    <span>{{ $isFr ? 'Page' : 'Page' }} {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
    @if($paginator->nextPageUrl())
        <a class="btn-secondary" rel="next" href="{{ $paginator->nextPageUrl() }}">{{ $isFr ? 'Suivant' : 'Next' }}</a>
    @else
        <span>{{ $isFr ? 'Dernière page' : 'Last page' }}</span>
    @endif
</nav>
@endif
