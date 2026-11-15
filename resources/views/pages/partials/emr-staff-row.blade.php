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
            <label>{{ $isFr ? 'Nom complet (comme sur la licence)' : 'Full name (as on licence)' }}</label>
            <input type="text" name="staff[{{ $i }}][full_name]" maxlength="120" value="{{ $row['full_name'] ?? '' }}" placeholder="{{ $isFr ? 'Dr. Mbarga Jean' : 'Dr. John Smith' }}">
        </div>
        <div class="df-group">
            <label>{{ $isFr ? 'Titre / Fonction' : 'Title / Role' }}</label>
            <select name="staff[{{ $i }}][job_role]" aria-label="{{ $isFr ? 'Titre' : 'Title' }}">
                <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                @foreach($jobRoles as $key => $labels)
                    <option value="{{ $key }}" {{ ($row['job_role'] ?? '') === $key ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'Spécialité' : 'Specialty' }}</label>
            <input type="text" name="staff[{{ $i }}][specialty]" maxlength="120" value="{{ $row['specialty'] ?? '' }}">
        </div>
        <div class="df-group">
            <label>{{ $isFr ? 'Accès requis' : 'Access needed' }}</label>
            <select name="staff[{{ $i }}][access_level]" aria-label="{{ $isFr ? 'Accès' : 'Access' }}">
                <option value="">{{ $isFr ? 'Sélectionner...' : 'Select...' }}</option>
                @foreach($accessLevels as $key => $labels)
                    <option value="{{ $key }}" {{ ($row['access_level'] ?? '') === $key ? 'selected' : '' }}>{{ $labels[$locale] ?? $labels['en'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'N° de licence / enregistrement' : 'Licence / registration no.' }}</label>
            <input type="text" name="staff[{{ $i }}][licence_number]" maxlength="80" value="{{ $row['licence_number'] ?? '' }}">
        </div>
        <div class="df-group">
            <label>{{ $isFr ? 'Autorité de délivrance' : 'Issuing body' }}</label>
            <input type="text" name="staff[{{ $i }}][licence_issuing_body]" maxlength="120" value="{{ $row['licence_issuing_body'] ?? '' }}">
        </div>
    </div>

    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'Expiration de la licence' : 'Licence expiry' }}</label>
            <input type="date" name="staff[{{ $i }}][licence_expiry]" value="{{ $row['licence_expiry'] ?? '' }}" aria-label="{{ $isFr ? 'Expiration de la licence' : 'Licence expiry' }}">
        </div>
        <div class="df-group">
            <label>{{ $isFr ? 'Années d\'expérience' : 'Years of experience' }}</label>
            <input type="number" min="0" max="80" name="staff[{{ $i }}][years_experience]" value="{{ $row['years_experience'] ?? '' }}">
        </div>
    </div>

    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'Site' : 'Site' }}</label>
            <input type="text" name="staff[{{ $i }}][site]" maxlength="160" value="{{ $row['site'] ?? '' }}">
        </div>
        <div class="df-group">
            <label>{{ $isFr ? 'Département' : 'Department' }}</label>
            <input type="text" name="staff[{{ $i }}][department]" maxlength="80" value="{{ $row['department'] ?? '' }}">
        </div>
    </div>

    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'Téléphone / WhatsApp' : 'Phone / WhatsApp' }}</label>
            <input type="tel" name="staff[{{ $i }}][phone]" maxlength="30" value="{{ $row['phone'] ?? '' }}" placeholder="+237 6XX XXX XXX">
        </div>
        <div class="df-group">
            <label>Email</label>
            <input type="email" name="staff[{{ $i }}][email]" maxlength="150" value="{{ $row['email'] ?? '' }}" placeholder="staff@hospital.cm">
        </div>
    </div>

    <div class="df-row">
        <div class="df-group">
            <label>{{ $isFr ? 'Jours & heures de travail' : 'Working days & hours' }}</label>
            <input type="text" name="staff[{{ $i }}][working_hours]" maxlength="160" value="{{ $row['working_hours'] ?? '' }}" placeholder="{{ $isFr ? 'Lun–Ven 8h–17h' : 'Mon–Fri 8am–5pm' }}">
        </div>
        <div class="df-group">
            <label>{{ $isFr ? 'Frais de consultation (si par médecin)' : 'Consultation fee (if per doctor)' }}</label>
            <input type="text" name="staff[{{ $i }}][consultation_fee]" maxlength="40" value="{{ $row['consultation_fee'] ?? '' }}">
        </div>
    </div>

    <div class="df-group">
        <label>{{ $isFr ? 'Signature (médecins — pour ordonnances & rapports)' : 'Signature image (doctors — for prescriptions & reports)' }}</label>
        <input type="file" name="staff[{{ $i }}][signature]" accept="image/*">
    </div>
</div>
