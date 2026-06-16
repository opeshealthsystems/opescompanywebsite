<x-layouts.auth title="Forgot Password">
    <div class="auth-card">
        <h1 class="auth-heading">Reset your password</h1>
        <p class="auth-subheading">Enter your email and we'll send a reset link.</p>

        @if (session('status'))
            <div class="auth-error-box" style="background:#dcfce7;border-color:#86efac;color:#166534;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf
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

            <button type="submit" class="auth-btn">Send Reset Link</button>
        </form>

        <p class="auth-switch">
            <a href="{{ route('login') }}" class="auth-link">&larr; Back to sign in</a>
        </p>
    </div>
</x-layouts.auth>
