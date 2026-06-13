<x-layouts.auth title="Create Account">
    <div class="auth-card auth-card-wide">
        <h1 class="auth-heading">Create your account</h1>
        <p class="auth-subheading">Join OPES Health Systems — digitising healthcare in Cameroon</p>

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="auth-form">
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

            <p class="auth-section-label" style="margin-top:1.5rem">Facility Information</p>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label for="facility_name" class="auth-label">Facility Name</label>
                    <input id="facility_name" name="facility_name" type="text" class="auth-input"
                        value="{{ old('facility_name') }}" placeholder="Central Hospital Douala">
                </div>
                <div class="auth-field">
                    <label for="facility_type" class="auth-label">Facility Type</label>
                    <select id="facility_type" name="facility_type" class="auth-input auth-select">
                        <option value="">— Select type —</option>
                        @foreach(['hospital'=>'Hospital','clinic'=>'Clinic','laboratory'=>'Laboratory','pharmacy'=>'Pharmacy','radiology'=>'Radiology Centre','nursing_home'=>'Nursing Home','other'=>'Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('facility_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="auth-field">
                    <label for="country" class="auth-label">Country *</label>
                    <input id="country" name="country" type="text" class="auth-input @error('country') auth-input-error @enderror"
                        value="{{ old('country', 'CM') }}" required placeholder="CM">
                </div>
                <div class="auth-field">
                    <label for="city" class="auth-label">City</label>
                    <input id="city" name="city" type="text" class="auth-input"
                        value="{{ old('city') }}" placeholder="Douala">
                </div>
            </div>

            <button type="submit" class="auth-btn" style="margin-top:1.5rem">Create Account</button>
        </form>

        <p class="auth-switch">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-link">Sign in</a>
        </p>
    </div>
</x-layouts.auth>
