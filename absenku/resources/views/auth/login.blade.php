<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Absenku</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --teal-400: #2dd4bf;
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
            --teal-700: #0f766e;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0f1e;
            overflow: hidden;
            position: relative;
        }

        /* ── Animated background ─────────────────────────────── */
        .bg-orbs {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: orbFloat 12s ease-in-out infinite;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #0d9488, transparent 70%);
            top: -120px; left: -80px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #7c3aed, transparent 70%);
            bottom: -80px; right: -60px;
            animation-delay: -5s;
        }
        .orb-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #0369a1, transparent 70%);
            top: 50%; right: 20%;
            animation-delay: -3s;
        }
        @keyframes orbFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        /* Grid dots overlay */
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: 28px 28px;
            z-index: 0;
        }

        /* ── Card ────────────────────────────────────────────── */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow:
                0 0 0 1px rgba(255,255,255,.05),
                0 20px 60px rgba(0,0,0,.5),
                inset 0 1px 0 rgba(255,255,255,.08);
        }

        /* ── Brand ───────────────────────────────────────────── */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px; height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--teal-500), #0369a1);
            margin-bottom: .875rem;
            box-shadow: 0 8px 24px rgba(13,148,136,.4);
        }
        .brand-icon svg { color: #fff; }
        .brand-name {
            font-size: 1.6rem;
            font-weight: 800;
            color: #f8fafc;
            letter-spacing: -.03em;
        }
        .brand-sub {
            font-size: .8rem;
            color: #64748b;
            margin-top: .25rem;
            letter-spacing: .01em;
        }

        /* ── Session status ──────────────────────────────────── */
        .session-status {
            background: rgba(13,148,136,.15);
            border: 1px solid rgba(13,148,136,.3);
            color: var(--teal-400);
            border-radius: .625rem;
            padding: .6rem .875rem;
            font-size: .82rem;
            margin-bottom: 1.25rem;
        }

        /* ── Form ────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.1rem; }

        label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: .4rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: .875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: .7rem 1rem .7rem 2.75rem;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.10);
            border-radius: .625rem;
            color: #f1f5f9;
            font-size: .9rem;
            font-family: inherit;
            transition: border-color .2s, box-shadow .2s, background .2s;
            outline: none;
        }
        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #475569;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--teal-500);
            background: rgba(13,148,136,.06);
            box-shadow: 0 0 0 3px rgba(13,148,136,.15);
        }
        input[type="email"].is-error,
        input[type="password"].is-error {
            border-color: #f43f5e;
        }

        .error-msg {
            color: #f87171;
            font-size: .75rem;
            margin-top: .35rem;
        }

        /* ── Remember + forgot ───────────────────────────────── */
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: .5rem;
            cursor: pointer;
        }
        .remember input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: var(--teal-500);
            cursor: pointer;
        }
        .remember span {
            font-size: .8rem;
            color: #64748b;
        }
        .forgot-link {
            font-size: .8rem;
            color: var(--teal-400);
            text-decoration: none;
            transition: color .2s;
        }
        .forgot-link:hover { color: #fff; }

        /* ── Submit button ───────────────────────────────────── */
        .btn-login {
            width: 100%;
            padding: .8rem 1.25rem;
            background: linear-gradient(135deg, var(--teal-600), var(--teal-500));
            border: none;
            border-radius: .625rem;
            color: #fff;
            font-size: .9rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            letter-spacing: .02em;
            transition: transform .15s, box-shadow .15s, filter .15s;
            box-shadow: 0 6px 20px rgba(13,148,136,.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
        }
        .btn-login:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(13,148,136,.45);
        }
        .btn-login:active {
            transform: translateY(0);
        }

        /* ── Back to home ────────────────────────────────────── */
        .back-home {
            text-align: center;
            margin-top: 1.5rem;
        }
        .back-home a {
            font-size: .8rem;
            color: #64748b;
            text-decoration: none;
            transition: color .2s;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }
        .back-home a:hover { color: var(--teal-400); }

        /* ── Divider ─────────────────────────────────────────── */
        .divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.25rem 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,.08);
        }
        .divider span {
            font-size: .72rem;
            color: #475569;
        }

        /* ── Decorative top line ─────────────────────────────── */
        .card-top-line {
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--teal-500), #7c3aed, transparent);
            border-radius: 99px;
            margin-bottom: 2rem;
            opacity: .6;
        }
    </style>
</head>
<body>

    <!-- Background -->
    <div class="bg-grid"></div>
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Login Card -->
    <div class="login-wrapper">
        <div class="card">

            <!-- Top accent line -->
            <div class="card-top-line"></div>

            <!-- Brand -->
            <div class="brand">
                <div class="brand-icon">
                    <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="brand-name">Absenku</div>
                <div class="brand-sub">Sistem Presensi Face Recognition</div>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="session-status">{{ session('status') }}</div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            required
                            autofocus
                            autocomplete="username"
                            class="{{ $errors->has('email') ? 'is-error' : '' }}"
                        >
                    </div>
                    @error('email')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            class="{{ $errors->has('password') ? 'is-error' : '' }}"
                        >
                    </div>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember + Forgot -->
                <div class="form-row">
                    <label class="remember">
                        <input type="checkbox" name="remember" id="remember_me">
                        <span>Ingat saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk ke Dashboard
                </button>
            </form>

            <!-- Back to home -->
            <div class="back-home">
                <a href="{{ url('/') }}">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke halaman utama
                </a>
            </div>

        </div>
    </div>

</body>
</html>
