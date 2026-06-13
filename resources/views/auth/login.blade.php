<x-layouts.auth title="Sign In">
    <div class="auth-card">
        <h1 class="auth-heading">Welcome back</h1>
        <p class="auth-subheading">Sign in to your OPES customer portal</p>

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="auth-form">
            @csrf
            <input type="hidden" name="locale" value="{{ request()->segment(1) === 'fr' ? 'fr' : 'en' }}">

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
                <label for="password" class="auth-label">Password</label>
                <input
                    id="password" name="password" type="password"
                    class="auth-input"
                    required autocomplete="current-password"
                    placeholder="••••••••"
                >
            </div>

            <div class="auth-remember">
                <label class="auth-check-label">
                    <input type="checkbox" name="remember" class="auth-check"> Remember me
                </label>
            </div>

            <button type="submit" class="auth-btn">Sign In</button>
        </form>

        <p class="auth-switch">
            Don't have an account?
            <a href="{{ route('register') }}" class="auth-link">Create one</a>
        </p>
    </div>
</x-layouts.auth>
