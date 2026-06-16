<x-layouts.auth title="Set New Password">
    <div class="auth-card">
        <h1 class="auth-heading">Set new password</h1>
        <p class="auth-subheading">Choose a strong password for your account.</p>

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="auth-field">
                <label for="email" class="auth-label">Email address</label>
                <input
                    id="email" name="email" type="email"
                    class="auth-input @error('email') auth-input-error @enderror"
                    value="{{ old('email') }}"
                    required autofocus autocomplete="email"
                    placeholder="you@facility.cm"
                >
            </div>

            <div class="auth-field">
                <label for="password" class="auth-label">New password</label>
                <input
                    id="password" name="password" type="password"
                    class="auth-input @error('password') auth-input-error @enderror"
                    required autocomplete="new-password"
                    placeholder="••••••••"
                >
            </div>

            <div class="auth-field">
                <label for="password_confirmation" class="auth-label">Confirm new password</label>
                <input
                    id="password_confirmation" name="password_confirmation" type="password"
                    class="auth-input"
                    required autocomplete="new-password"
                    placeholder="••••••••"
                >
            </div>

            <button type="submit" class="auth-btn">Reset Password</button>
        </form>
    </div>
</x-layouts.auth>
