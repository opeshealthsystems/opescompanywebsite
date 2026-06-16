<x-layouts.auth title="Practitioner Registration">
    <div class="auth-card auth-card-wide">
        <h1 class="auth-heading">Join the Practitioner Programme</h1>
        <p class="auth-subheading">Register as a verified healthcare professional on OPES Health Systems</p>

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('practitioner.register.post') }}" class="auth-form">
            @csrf
            <input type="hidden" name="locale" value="{{ request()->segment(1) === 'fr' ? 'fr' : 'en' }}">

            <p class="auth-section-label">Contact Information</p>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label for="name" class="auth-label">Full Name *</label>
                    <input id="name" name="name" type="text" class="auth-input @error('name') auth-input-error @enderror"
                        value="{{ old('name') }}" required placeholder="Dr. Ambe John">
                </div>
                <div class="auth-field">
                    <label for="email" class="auth-label">Email Address *</label>
                    <input id="email" name="email" type="email" class="auth-input @error('email') auth-input-error @enderror"
                        value="{{ old('email') }}" required placeholder="you@hospital.cm">
                </div>
                <div class="auth-field">
                    <label for="password" class="auth-label">Password *</label>
                    <input id="password" name="password" type="password" class="auth-input"
                        required minlength="8" placeholder="Min. 8 characters">
                </div>
                <div class="auth-field">
                    <label for="password_confirmation" class="auth-label">Confirm Password *</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="auth-input"
                        required placeholder="Repeat password">
                </div>
                <div class="auth-field">
                    <label for="phone" class="auth-label">Phone</label>
                    <input id="phone" name="phone" type="tel" class="auth-input"
                        value="{{ old('phone') }}" placeholder="+237 6XX XXX XXX">
                </div>
            </div>

            <p class="auth-section-label" style="margin-top:1.5rem">Professional Information</p>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label for="profession" class="auth-label">Profession *</label>
                    <select id="profession" name="profession" class="auth-input auth-select @error('profession') auth-input-error @enderror" required>
                        <option value="">— Select profession —</option>
                        @foreach($professions as $val => $label)
                            <option value="{{ $val }}" {{ old('profession') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="auth-field">
                    <label for="specialty" class="auth-label">Specialty</label>
                    <input id="specialty" name="specialty" type="text" class="auth-input"
                        value="{{ old('specialty') }}" placeholder="e.g. Cardiology">
                </div>
                <div class="auth-field">
                    <label for="workplace_name" class="auth-label">Workplace / Hospital Name</label>
                    <input id="workplace_name" name="workplace_name" type="text" class="auth-input"
                        value="{{ old('workplace_name') }}" placeholder="Central Hospital Douala">
                </div>
                <div class="auth-field">
                    <label for="workplace_country" class="auth-label">Country</label>
                    <input id="workplace_country" name="workplace_country" type="text" class="auth-input"
                        value="{{ old('workplace_country', 'CM') }}" placeholder="CM">
                </div>
            </div>

            <button type="submit" class="auth-btn" style="margin-top:1.5rem">Create Practitioner Account</button>
        </form>

        <p class="auth-switch">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-link">Sign in</a>
        </p>
    </div>
</x-layouts.auth>
