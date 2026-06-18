<x-layouts.tester title="My Profile">

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">My Profile</h1>
        <p class="cp-page-subtitle">Update your account details</p>
    </div>
</div>

@if(session('success'))
<div class="cp-flash-success mb-4">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Avatar / info card --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 flex flex-col items-center text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 text-2xl font-bold text-white"
             style="background:linear-gradient(135deg,#1A6FE8,#00C896)">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <p class="text-lg font-semibold text-white">{{ $user->name }}</p>
        <p class="text-sm text-slate-400 mb-1">{{ $user->email }}</p>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-900/40 text-blue-300 border border-blue-800 mt-2">
            Tester
        </span>
        <p class="text-xs text-slate-500 mt-4">Member since {{ $user->created_at->format('M Y') }}</p>
    </div>

    {{-- Edit form --}}
    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-sm font-semibold text-white mb-5">Account Information</h2>
        <form method="POST" action="{{ route('tester.profile.update', ['locale' => app()->getLocale()]) }}">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500"
                           required maxlength="100">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500"
                           maxlength="30">
                    @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Email (read-only)</label>
                    <input type="text" value="{{ $user->email }}" disabled
                           class="w-full bg-slate-800/50 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white"
                        style="background:linear-gradient(135deg,#1A6FE8,#1258c4)">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.tester>
