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

        <form id="login-form" method="POST" action="{{ route('login.post') }}" class="auth-form">
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

            <div class="auth-remember" style="display:flex;align-items:center;justify-content:space-between;">
                <label class="auth-check-label">
                    <input type="checkbox" name="remember" class="auth-check"> Remember me
                </label>
                <a href="{{ route('password.request') }}" class="auth-link" style="font-size:0.8rem;">Forgot password?</a>
            </div>

            <button type="submit" class="auth-btn">Sign In</button>
        </form>

        <div style="margin-top:1.75rem;">
            <p style="color:#64748b;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.75rem;">Demo logins</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
                @foreach([
                    ['label' => 'Admin',    'email' => 'admin@demo.opes',    'color' => '#7c3aed'],
                    ['label' => 'Support',  'email' => 'support@demo.opes',  'color' => '#0ea5e9'],
                    ['label' => 'Customer', 'email' => 'customer@demo.opes', 'color' => '#00C896'],
                    ['label' => 'Tester',   'email' => 'tester@demo.opes',   'color' => '#f59e0b'],
                ] as $demo)
                <button
                    type="button"
                    onclick="demoLogin('{{ $demo['email'] }}')"
                    style="
                        background: transparent;
                        border: 1px solid {{ $demo['color'] }}44;
                        border-radius: 8px;
                        padding: 0.5rem 0.75rem;
                        cursor: pointer;
                        text-align: left;
                        transition: background 0.15s;
                    "
                    onmouseover="this.style.background='{{ $demo['color'] }}18'"
                    onmouseout="this.style.background='transparent'"
                >
                    <span style="display:block;color:{{ $demo['color'] }};font-size:0.75rem;font-weight:700;">{{ $demo['label'] }}</span>
                    <span style="display:block;color:#94a3b8;font-size:0.7rem;margin-top:1px;">{{ $demo['email'] }}</span>
                </button>
                @endforeach
            </div>
        </div>

        <p class="auth-switch">
            Don't have an account?
            <a href="{{ route('register') }}" class="auth-link">Create one</a>
        </p>
    </div>

    <script>
    function demoLogin(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'demo1234';
        document.getElementById('login-form').submit();
    }
    </script>
</x-layouts.auth>
