<x-layouts.practitioner title="My Profile">
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">My Profile</h1>
            <p class="text-slate-400 text-sm">Manage your account and professional information</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-900/40 border border-emerald-700 rounded-lg px-5 py-3 mb-6 text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Profile Form ─────────────────────────────────────────── --}}
    <form method="POST" action="{{ route('practitioner.profile.update', ['locale' => app()->getLocale()]) }}" class="mb-8">
        @csrf
        @method('PUT')

        @if ($errors->hasAny(['name', 'phone', 'profession', 'specialty', 'workplace_name', 'workplace_city', 'workplace_country', 'registration_number', 'years_of_experience', 'bio', 'opes_testimonial']))
            <div class="bg-red-900/40 border border-red-700 rounded-lg px-5 py-3 mb-6 text-red-300 text-sm">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
            <h2 class="flex items-center gap-2 text-base font-semibold text-white mb-5">
                <i data-lucide="user" style="width:18px;height:18px;color:#00C896"></i> Contact Details
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="name" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Full Name *</label>
                    <input id="name" name="name" type="text"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 @error('name') border-red-500 @enderror"
                        value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="email" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Email Address</label>
                    <input id="email" type="email"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed"
                        value="{{ $user->email }}" disabled
                        title="Contact support to change email">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="phone" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Phone</label>
                    <input id="phone" name="phone" type="tel"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"
                        value="{{ old('phone', $user->phone) }}" placeholder="+237 6XX XXX XXX">
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
            <h2 class="flex items-center gap-2 text-base font-semibold text-white mb-5">
                <i data-lucide="stethoscope" style="width:18px;height:18px;color:#00C896"></i> Professional Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="profession" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Profession *</label>
                    <select id="profession" name="profession"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500 @error('profession') border-red-500 @enderror"
                        required>
                        <option value="">— Select —</option>
                        @foreach($professions as $val => $label)
                        <option value="{{ $val }}" {{ old('profession', $profile?->profession) === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="specialty" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Specialty</label>
                    <input id="specialty" name="specialty" type="text"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"
                        value="{{ old('specialty', $profile?->specialty) }}" placeholder="e.g. Cardiology">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="registration_number" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Registration Number</label>
                    <input id="registration_number" name="registration_number" type="text"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"
                        value="{{ old('registration_number', $profile?->registration_number) }}" placeholder="License / registration number">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="years_of_experience" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Years of Experience</label>
                    <input id="years_of_experience" name="years_of_experience" type="number" min="0" max="60"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 @error('years_of_experience') border-red-500 @enderror"
                        value="{{ old('years_of_experience', $profile?->years_of_experience) }}">
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
            <h2 class="flex items-center gap-2 text-base font-semibold text-white mb-5">
                <i data-lucide="building-2" style="width:18px;height:18px;color:#00C896"></i> Workplace
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1 sm:col-span-2">
                    <label for="workplace_name" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Workplace Name</label>
                    <input id="workplace_name" name="workplace_name" type="text"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"
                        value="{{ old('workplace_name', $profile?->workplace_name) }}" placeholder="Hospital or clinic name">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="workplace_city" class="text-xs font-medium text-slate-400 uppercase tracking-wide">City</label>
                    <input id="workplace_city" name="workplace_city" type="text"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"
                        value="{{ old('workplace_city', $profile?->workplace_city) }}" placeholder="Douala">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="workplace_country" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Country</label>
                    <input id="workplace_country" name="workplace_country" type="text"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"
                        value="{{ old('workplace_country', $profile?->workplace_country ?? 'CM') }}" placeholder="CM">
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
            <h2 class="flex items-center gap-2 text-base font-semibold text-white mb-5">
                <i data-lucide="file-text" style="width:18px;height:18px;color:#00C896"></i> About You
            </h2>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <label for="bio" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Bio</label>
                    <textarea id="bio" name="bio" rows="4"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 resize-y"
                        placeholder="Brief professional biography…">{{ old('bio', $profile?->bio) }}</textarea>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="opes_testimonial" class="text-xs font-medium text-slate-400 uppercase tracking-wide">OPES Testimonial</label>
                    <textarea id="opes_testimonial" name="opes_testimonial" rows="3"
                        class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 resize-y"
                        placeholder="Share your experience with OPES Health Systems…">{{ old('opes_testimonial', $profile?->opes_testimonial) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-colors border-0 cursor-pointer">
                Save Changes
            </button>
            <a href="{{ route('practitioner.dashboard', ['locale' => app()->getLocale()]) }}"
               class="px-6 py-2.5 border border-slate-600 hover:border-slate-400 text-slate-300 hover:text-white text-sm font-medium rounded-lg transition-colors no-underline">
                Cancel
            </a>
        </div>
    </form>

    {{-- ── Change Password ──────────────────────────────────────── --}}
    <form method="POST" action="{{ route('practitioner.profile.password', ['locale' => app()->getLocale()]) }}">
        @csrf
        @method('PUT')

        @if ($errors->hasAny(['current_password', 'password']))
        <div class="bg-red-900/40 border border-red-700 rounded-lg px-5 py-3 mb-6 text-red-300 text-sm">
            @foreach($errors->get('current_password') as $e)<p>{{ $e }}</p>@endforeach
            @foreach($errors->get('password') as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
            <h2 class="flex items-center gap-2 text-base font-semibold text-white mb-5">
                <i data-lucide="lock" style="width:18px;height:18px;color:#1A6FE8"></i> Change Password
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1 sm:col-span-2">
                    <label for="current_password" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Current Password *</label>
                    <input id="current_password" name="current_password" type="password"
                           class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                           autocomplete="current-password" required>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="password" class="text-xs font-medium text-slate-400 uppercase tracking-wide">New Password *</label>
                    <input id="password" name="password" type="password"
                           class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 @error('password') border-red-500 @enderror"
                           autocomplete="new-password" required placeholder="Minimum 8 characters">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="password_confirmation" class="text-xs font-medium text-slate-400 uppercase tracking-wide">Confirm New Password *</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-blue-500"
                           autocomplete="new-password" required>
                </div>
            </div>
        </div>

        <div>
            <button type="submit"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition-colors border-0 cursor-pointer">
                Update Password
            </button>
        </div>
    </form>
</x-layouts.practitioner>
