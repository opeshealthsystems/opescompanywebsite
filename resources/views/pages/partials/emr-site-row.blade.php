@php
    $row = $row ?? [];
@endphp
<div class="staff-row" data-site-row>
    <div class="staff-row-head">
        <span class="staff-row-title">{{ $isFr ? 'Site' : 'Site' }}</span>
        <button type="button" class="staff-remove" data-site-remove aria-label="{{ $isFr ? 'Retirer' : 'Remove' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            {{ $isFr ? 'Retirer' : 'Remove' }}
        </button>
    </div>
    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'Nom du site' : 'Site name' }}</label>
            <input type="text" name="sites[{{ $i }}][name]" maxlength="160" value="{{ $row['name'] ?? '' }}">
        </div>
        <div class="df-group">
            <label>{{ $isFr ? 'Adresse' : 'Address' }}</label>
            <input type="text" name="sites[{{ $i }}][address]" maxlength="255" value="{{ $row['address'] ?? '' }}">
        </div>
    </div>
</div>
