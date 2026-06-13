<x-layouts.customer title="My Profile">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">My Profile</h1>
            <p class="cp-page-subtitle">Manage your account and facility information</p>
        </div>
    </div>

    <form method="POST" action="{{ route('customer.profile.update', ['locale' => app()->getLocale()]) }}" class="cp-form">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="cp-section-card">
            <h2 class="cp-section-title" style="margin-bottom:1.5rem">
                <i data-lucide="user" style="width:18px;height:18px;color:#00C896"></i> Contact Details
            </h2>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label for="name" class="auth-label">Full Name *</label>
                    <input id="name" name="name" type="text" class="auth-input @error('name') auth-input-error @enderror"
                        value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="auth-field">
                    <label class="auth-label">Email Address</label>
                    <input type="email" class="auth-input" value="{{ $user->email }}" disabled
                        style="opacity:0.5;cursor:not-allowed" title="Contact support to change email">
                </div>
                <div class="auth-field">
                    <label for="phone" class="auth-label">Phone</label>
                    <input id="phone" name="phone" type="tel" class="auth-input"
                        value="{{ old('phone', $user->phone) }}" placeholder="+237 6XX XXX XXX">
                </div>
            </div>
        </div>

        <div class="cp-section-card" style="margin-top:1.5rem">
            <h2 class="cp-section-title" style="margin-bottom:1.5rem">
                <i data-lucide="building-2" style="width:18px;height:18px;color:#00C896"></i> Facility Information
            </h2>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label for="facility_name" class="auth-label">Facility Name</label>
                    <input id="facility_name" name="facility_name" type="text" class="auth-input"
                        value="{{ old('facility_name', $profile->facility_name) }}" placeholder="Central Hospital Douala">
                </div>
                <div class="auth-field">
                    <label for="facility_type" class="auth-label">Facility Type</label>
                    <select id="facility_type" name="facility_type" class="auth-input auth-select">
                        <option value="">— Select —</option>
                        @foreach(['hospital'=>'Hospital','clinic'=>'Clinic','laboratory'=>'Laboratory','pharmacy'=>'Pharmacy','radiology'=>'Radiology Centre','nursing_home'=>'Nursing Home','other'=>'Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('facility_type', $profile->facility_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="auth-field">
                    <label for="country" class="auth-label">Country *</label>
                    <input id="country" name="country" type="text" class="auth-input @error('country') auth-input-error @enderror"
                        value="{{ old('country', $profile->country) }}" required>
                </div>
                <div class="auth-field">
                    <label for="city" class="auth-label">City</label>
                    <input id="city" name="city" type="text" class="auth-input"
                        value="{{ old('city', $profile->city) }}" placeholder="Douala">
                </div>
                <div class="auth-field" style="grid-column: 1 / -1">
                    <label for="address" class="auth-label">Address</label>
                    <input id="address" name="address" type="text" class="auth-input"
                        value="{{ old('address', $profile->address) }}" placeholder="Street address">
                </div>
            </div>
        </div>

        <div style="margin-top:1.5rem; display:flex; gap:1rem; align-items:center">
            <button type="submit" class="auth-btn" style="width:auto; padding:0.75rem 2rem">Save Changes</button>
            <a href="{{ route('customer.dashboard', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">
                Cancel
            </a>
        </div>
    </form>
</x-layouts.customer>
