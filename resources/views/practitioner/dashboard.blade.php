<x-layouts.practitioner title="Dashboard">
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Welcome, {{ $user->name }}</h1>
            <p class="text-slate-400 text-sm">
                @if($profile)
                    {{ $profile->profession ?? 'Practitioner' }}
                    @if($profile->workplace_name) · {{ $profile->workplace_name }} @endif
                    @if($profile->workplace_city) · {{ $profile->workplace_city }} @endif
                @else
                    Your OPES Practitioner account
                @endif
            </p>
        </div>
        <a href="{{ route('practitioner.profile', ['locale' => app()->getLocale()]) }}"
           class="flex items-center gap-2 px-4 py-2 rounded border border-slate-600 text-slate-300 text-sm hover:border-slate-400 hover:text-white transition-colors no-underline">
            <i data-lucide="settings" style="width:15px;height:15px"></i> Edit Profile
        </a>
    </div>

    @if(!$profile)
    <div class="bg-amber-900/30 border border-amber-700 rounded-lg px-5 py-4 mb-6 flex items-start gap-3">
        <i data-lucide="alert-triangle" style="width:20px;height:20px;color:#f59e0b;flex-shrink:0;margin-top:2px"></i>
        <div>
            <p class="text-amber-300 font-medium text-sm mb-0.5">Complete your profile</p>
            <p class="text-amber-400/80 text-sm">Please fill in your professional details so we can match you with the right programs and opportunities.</p>
            <a href="{{ route('practitioner.profile', ['locale' => app()->getLocale()]) }}"
               class="inline-block mt-2 text-sm text-amber-300 underline hover:text-amber-100">Go to Profile →</a>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="clipboard-list" style="width:20px;height:20px;color:#00C896"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">0</p>
                <p class="text-xs text-slate-400">Active Applications</p>
            </div>
        </div>
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(26,111,232,0.1)">
                <i data-lucide="bar-chart-2" style="width:20px;height:20px;color:#1A6FE8"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">0</p>
                <p class="text-xs text-slate-400">Surveys Pending</p>
            </div>
        </div>
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(234,179,8,0.1)">
                <i data-lucide="award" style="width:20px;height:20px;color:#eab308"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">0</p>
                <p class="text-xs text-slate-400">Certificates</p>
            </div>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 flex items-start gap-4">
        <i data-lucide="life-buoy" style="width:24px;height:24px;color:#00C896;flex-shrink:0;margin-top:2px"></i>
        <div>
            <p class="font-semibold text-white mb-1">Need help?</p>
            <p class="text-sm text-slate-400">Our team is available Mon–Fri 8 am – 6 pm (WAT). Email
                <a href="mailto:support@opeshealthsystems.com" class="text-emerald-400 hover:underline">support@opeshealthsystems.com</a>
                or visit our <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="text-emerald-400 hover:underline">contact page</a>.
            </p>
        </div>
    </div>
</x-layouts.practitioner>
