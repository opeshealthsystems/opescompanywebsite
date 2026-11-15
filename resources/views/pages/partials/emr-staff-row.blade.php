@php
    /** @var string|int $i  Row index (or the literal __IDX__ placeholder for the JS template) */
    $row = $row ?? [];
@endphp
<div class="staff-row" data-staff-row>
    <div class="staff-row-head">
        <span class="staff-row-title">{{ $isFr ? 'Membre du personnel' : 'Staff member' }}</span>
        <button type="button" class="staff-remove" data-staff-remove aria-label="{{ $isFr ? 'Retirer' : 'Remove' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            {{ $isFr ? 'Retirer' : 'Remove' }}
        </button>
    </div>

    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'Nom complet' : 'Full name' }}</label>
            <input type="text" name="staff[{{ $i }}][full_name]" maxlength="120" value="{{ $row['full_name'] ?? '' }}" placeholder="{{ $isFr ? 'Dr. Mbarga Jean' : 'Dr. John Smith' }}">
        </div>
        <div class="df-group">
            <label>{{ $isFr ? 'Fonction' : 'Job role' }}</label>
            <select name="staff[{{ $i }}][job_role]" aria-label="{{ $isFr ? 'Fonction' : 'Job role' }}">
                <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                @foreach($jobRoles as $key => $labels)
                    <option value="{{ $key }}" {{ ($row['job_role'] ?? '') === $key ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'Département / Unité' : 'Department / Unit' }}</label>
            <input type="text" name="staff[{{ $i }}][department]" maxlength="80" value="{{ $row['department'] ?? '' }}" placeholder="{{ $isFr ? 'Laboratoire, Pharmacie...' : 'Lab, Pharmacy...' }}">
        </div>
        <div class="df-group">
            <label>{{ $isFr ? 'Niveau d\'accès souhaité' : 'Intended access level' }}</label>
            <select name="staff[{{ $i }}][access_level]" aria-label="{{ $isFr ? 'Niveau d\'accès' : 'Access level' }}">
                <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                @foreach($accessLevels as $key => $labels)
                    <option value="{{ $key }}" {{ ($row['access_level'] ?? '') === $key ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'Téléphone' : 'Phone' }}</label>
            <input type="tel" name="staff[{{ $i }}][phone]" maxlength="30" value="{{ $row['phone'] ?? '' }}" placeholder="+237 6XX XXX XXX">
        </div>
        <div class="df-group">
            <label>Email</label>
            <input type="email" name="staff[{{ $i }}][email]" maxlength="150" value="{{ $row['email'] ?? '' }}" placeholder="staff@hospital.cm">
        </div>
    </div>

    <label class="staff-task-toggle">
        <input type="checkbox" name="staff[{{ $i }}][takes_tasks]" value="1" {{ ! empty($row['takes_tasks']) ? 'checked' : '' }}>
        <span>{{ $isFr ? 'Cette personne prend / gère des tâches dans le système' : 'This person takes / handles tasks in the system' }}</span>
    </label>
</div>
