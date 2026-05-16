@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
<div class="auth-shell">
    {{-- Animated mesh background --}}
    <div class="auth-mesh" aria-hidden="true">
        <span class="orb orb-1"></span>
        <span class="orb orb-2"></span>
        <span class="orb orb-3"></span>
        <span class="orb orb-4"></span>
        <div class="auth-grid"></div>
    </div>

    {{-- Floating glass theme toggle --}}
    <button type="button" class="auth-theme-toggle" id="authThemeToggle" title="Toggle light / dark mode" aria-label="Toggle theme">
        <i class="bi" id="authThemeIcon"></i>
    </button>

    <div class="auth-container">
        <div class="auth-grid-2">
            {{-- Left: hero / brand panel (desktop only) --}}
            <aside class="auth-hero d-none d-lg-flex">
                <div class="auth-hero-inner">
                    <a href="{{ url('/') }}" class="auth-hero-brand">
                        <span class="auth-brand-mark">@include('partials._brand_logo')</span>
                        <span class="auth-hero-brand-text">
                            <span class="auth-hero-brand-name">{{ config('app.name', 'ITAMS') }}</span>
                            <span class="auth-hero-brand-sub">IT Assets Management</span>
                        </span>
                    </a>

                    <div class="auth-hero-pitch">
                        <span class="auth-pill">
                            <span class="auth-pill-dot"></span>
                            Enterprise IT &middot; v1.0
                        </span>
                        <h2 class="auth-hero-title">
                            One source of truth for every asset, license, and renewal.
                        </h2>
                        <p class="auth-hero-lead">
                            Track PCs, devices, subscriptions and contracts &mdash; with smart reminders before anything expires.
                        </p>
                    </div>

                    <ul class="auth-feature-list">
                        <li>
                            <span class="auth-feature-icon"><i class="bi bi-shield-lock-fill"></i></span>
                            <div>
                                <div class="auth-feature-title">Role-based access</div>
                                <div class="auth-feature-sub">Granular module permissions for every team member.</div>
                            </div>
                        </li>
                        <li>
                            <span class="auth-feature-icon"><i class="bi bi-bell-fill"></i></span>
                            <div>
                                <div class="auth-feature-title">Renewal intelligence</div>
                                <div class="auth-feature-sub">Daily reminders so contracts never lapse silently.</div>
                            </div>
                        </li>
                        <li>
                            <span class="auth-feature-icon"><i class="bi bi-graph-up-arrow"></i></span>
                            <div>
                                <div class="auth-feature-title">Live inventory insights</div>
                                <div class="auth-feature-sub">Real-time dashboards across PC and device fleets.</div>
                            </div>
                        </li>
                    </ul>

                    <div class="auth-hero-foot">
                        <span>&copy; {{ date('Y') }} {{ config('app.name', 'ITAMS') }}</span>
                        <span class="auth-dot">&middot;</span>
                        <span>All systems operational</span>
                    </div>
                </div>
            </aside>

            {{-- Right: glass form card --}}
            <main class="auth-panel">
                <div class="auth-card">
                    <div class="auth-card-shine" aria-hidden="true"></div>

                    {{-- Mobile-only brand row --}}
                    <a href="{{ url('/') }}" class="auth-mobile-brand d-flex d-lg-none">
                        <span class="auth-brand-mark">@include('partials._brand_logo')</span>
                        <span class="auth-hero-brand-text">
                            <span class="auth-hero-brand-name">{{ config('app.name', 'ITAMS') }}</span>
                            <span class="auth-hero-brand-sub">IT Assets Management</span>
                        </span>
                    </a>

                    <header class="auth-card-header">
                        <h1 class="auth-title">Welcome back</h1>
                        <p class="auth-subtitle">Sign in to manage your IT assets.</p>
                    </header>

                    @if($errors->any())
                        <div class="auth-alert" role="alert">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" id="loginForm" class="auth-form" novalidate>
                        @csrf

                        <div class="auth-field" data-field="email">
                            <label for="email" class="auth-label">Email</label>
                            <div class="auth-input">
                                <i class="bi bi-envelope auth-input-icon"></i>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="auth-input-control @error('email') has-error @enderror"
                                    placeholder="you@company.com"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    spellcheck="false">
                            </div>
                        </div>

                        <div class="auth-field" data-field="password">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <label for="password" class="auth-label">Password</label>
                                <span class="auth-label-hint">8+ characters</span>
                            </div>
                            <div class="auth-input">
                                <i class="bi bi-lock auth-input-icon"></i>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="auth-input-control @error('password') has-error @enderror"
                                    placeholder="Enter your password"
                                    required
                                    autocomplete="current-password">
                                <button
                                    type="button"
                                    class="auth-input-reveal"
                                    id="togglePassword"
                                    tabindex="-1"
                                    aria-label="Show password"
                                    title="Show / hide password">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="auth-row">
                            <label class="auth-check">
                                <input type="checkbox" name="remember" id="remember">
                                <span class="auth-check-box" aria-hidden="true"><i class="bi bi-check2"></i></span>
                                <span class="auth-check-label">Keep me signed in</span>
                            </label>
                        </div>

                        <button type="submit" class="auth-submit">
                            <span class="auth-submit-shine" aria-hidden="true"></span>
                            <span class="default">
                                <span>Sign in</span>
                                <i class="bi bi-arrow-right"></i>
                            </span>
                            <span class="loading d-none">
                                <span class="spinner-border spinner-border-sm"></span>
                                <span>Signing in…</span>
                            </span>
                        </button>

                        <p class="auth-help">
                            Trouble signing in?
                            <a href="mailto:infrastructure@brycenmyanmar.com.mm">Contact IT support</a>
                        </p>
                    </form>
                </div>

                <div class="auth-footer d-lg-none">
                    <span>{{ config('app.name', 'ITAMS') }}</span>
                    <span class="auth-dot">&middot;</span>
                    <span>&copy; {{ date('Y') }}</span>
                </div>
            </main>
        </div>
    </div>
</div>

<style>
    /* ---------- Layout shell ---------- */
    .auth-shell {
        min-height: 100vh;
        min-height: 100dvh;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(1rem, 3vw, 2.5rem);
        font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: .9rem;
        color: #0f172a;
        background: linear-gradient(135deg, #eef2ff 0%, #f5f7fb 45%, #ecfeff 100%);
    }
    [data-bs-theme="dark"] .auth-shell {
        color: #e2e8f0;
        background: radial-gradient(circle at 20% 10%, #161a2c 0%, #0b1020 55%, #060912 100%);
    }

    /* ---------- Animated mesh background ---------- */
    .auth-mesh {
        position: absolute;
        inset: 0;
        z-index: 0;
        overflow: hidden;
        pointer-events: none;
    }
    .auth-mesh .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: .55;
        will-change: transform;
        animation: orbDrift 18s ease-in-out infinite;
    }
    .auth-mesh .orb-1 {
        width: 520px; height: 520px;
        top: -160px; left: -120px;
        background: radial-gradient(circle, #6366f1 0%, rgba(99,102,241,0) 70%);
        animation-delay: -2s;
    }
    .auth-mesh .orb-2 {
        width: 460px; height: 460px;
        bottom: -180px; right: -140px;
        background: radial-gradient(circle, #06b6d4 0%, rgba(6,182,212,0) 70%);
        animation-delay: -7s;
    }
    .auth-mesh .orb-3 {
        width: 380px; height: 380px;
        top: 40%; left: 55%;
        background: radial-gradient(circle, #a855f7 0%, rgba(168,85,247,0) 70%);
        opacity: .35;
        animation-delay: -12s;
    }
    .auth-mesh .orb-4 {
        width: 320px; height: 320px;
        top: 10%; right: 18%;
        background: radial-gradient(circle, #22c55e 0%, rgba(34,197,94,0) 70%);
        opacity: .25;
        animation-delay: -4s;
    }
    [data-bs-theme="dark"] .auth-mesh .orb { opacity: .45; }
    [data-bs-theme="dark"] .auth-mesh .orb-3 { opacity: .3; }
    [data-bs-theme="dark"] .auth-mesh .orb-4 { opacity: .22; }

    .auth-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(15,23,42,0.045) 1px, transparent 1px),
            linear-gradient(90deg, rgba(15,23,42,0.045) 1px, transparent 1px);
        background-size: 48px 48px;
        mask-image: radial-gradient(ellipse at center, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0) 75%);
        -webkit-mask-image: radial-gradient(ellipse at center, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0) 75%);
    }
    [data-bs-theme="dark"] .auth-grid {
        background-image:
            linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
    }

    @keyframes orbDrift {
        0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
        33%      { transform: translate3d(40px, -30px, 0) scale(1.05); }
        66%      { transform: translate3d(-30px, 30px, 0) scale(.97); }
    }

    /* ---------- Container ---------- */
    .auth-container {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 1080px;
    }
    .auth-grid-2 {
        display: grid;
        grid-template-columns: 1fr;
        gap: clamp(1.5rem, 3vw, 2.5rem);
        align-items: stretch;
    }
    @media (min-width: 992px) {
        .auth-grid-2 { grid-template-columns: 1.05fr .95fr; }
    }

    /* ---------- Hero panel ---------- */
    .auth-hero {
        position: relative;
        border-radius: 1.75rem;
        padding: clamp(1.75rem, 2.4vw, 2.5rem);
        background:
            linear-gradient(155deg, rgba(255,255,255,0.7) 0%, rgba(255,255,255,0.35) 60%, rgba(255,255,255,0.5) 100%);
        border: 1px solid rgba(255,255,255,0.7);
        box-shadow:
            0 30px 80px rgba(31,38,135,0.12),
            inset 0 1px 0 rgba(255,255,255,0.9);
        backdrop-filter: blur(28px) saturate(180%);
        -webkit-backdrop-filter: blur(28px) saturate(180%);
        overflow: hidden;
    }
    [data-bs-theme="dark"] .auth-hero {
        background:
            linear-gradient(155deg, rgba(30,36,55,0.7) 0%, rgba(20,24,40,0.5) 60%, rgba(30,36,55,0.65) 100%);
        border-color: rgba(255,255,255,0.08);
        box-shadow:
            0 30px 80px rgba(0,0,0,0.55),
            inset 0 1px 0 rgba(255,255,255,0.06);
    }
    .auth-hero-inner {
        display: flex;
        flex-direction: column;
        height: 100%;
        gap: 1.75rem;
    }
    .auth-hero-brand {
        display: inline-flex;
        align-items: center;
        gap: .75rem;
        text-decoration: none;
        color: inherit;
        width: fit-content;
    }
    .auth-brand-mark {
        position: relative;
        width: 44px;
        height: 44px;
        border-radius: .8rem;
        background: linear-gradient(135deg, #5b6cff 0%, #8b5cf6 50%, #ec4899 100%);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow:
            0 12px 28px rgba(91,108,255,0.4),
            inset 0 1px 0 rgba(255,255,255,0.35),
            inset 0 -1px 0 rgba(0,0,0,0.1);
        flex-shrink: 0;
        overflow: hidden;
    }
    .auth-brand-mark::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.4), transparent 55%);
        pointer-events: none;
    }
    .auth-brand-mark .rrs-logo { display: block; position: relative; z-index: 1; }
    .auth-hero-brand-text { display: flex; flex-direction: column; line-height: 1.15; }
    .auth-hero-brand-name { font-weight: 700; font-size: 1.05rem; letter-spacing: -.01em; }
    .auth-hero-brand-sub { font-size: .72rem; color: #64748b; letter-spacing: .02em; text-transform: uppercase; }
    [data-bs-theme="dark"] .auth-hero-brand-sub { color: #94a3b8; }

    .auth-hero-pitch { display: flex; flex-direction: column; gap: 1rem; }
    .auth-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        background: rgba(255,255,255,0.65);
        border: 1px solid rgba(15,23,42,0.06);
        border-radius: 999px;
        padding: .35rem .75rem;
        font-size: .72rem;
        font-weight: 600;
        color: #475569;
        letter-spacing: .02em;
        width: fit-content;
        backdrop-filter: blur(8px);
    }
    .auth-pill-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
        animation: pulse 2.4s ease-in-out infinite;
    }
    [data-bs-theme="dark"] .auth-pill {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.08);
        color: #cbd5e1;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }
        50%      { box-shadow: 0 0 0 6px rgba(34,197,94,0.0); }
    }

    .auth-hero-title {
        font-size: clamp(1.65rem, 2.4vw, 2.1rem);
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -.02em;
        margin: 0;
        color: #0f172a;
        background: linear-gradient(135deg, #0f172a 0%, #4338ca 50%, #7c3aed 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    [data-bs-theme="dark"] .auth-hero-title {
        background: linear-gradient(135deg, #f1f5f9 0%, #c4b5fd 50%, #93c5fd 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .auth-hero-lead {
        font-size: .95rem;
        color: #475569;
        margin: 0;
        line-height: 1.55;
        max-width: 38ch;
    }
    [data-bs-theme="dark"] .auth-hero-lead { color: #94a3b8; }

    .auth-feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: .85rem;
        flex-grow: 1;
    }
    .auth-feature-list li {
        display: flex;
        align-items: flex-start;
        gap: .85rem;
        padding: .85rem 1rem;
        background: rgba(255,255,255,0.5);
        border: 1px solid rgba(15,23,42,0.05);
        border-radius: .9rem;
        backdrop-filter: blur(8px);
        transition: transform .25s ease, background .25s ease, border-color .25s ease;
    }
    .auth-feature-list li:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.75);
        border-color: rgba(91,108,255,0.25);
    }
    [data-bs-theme="dark"] .auth-feature-list li {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.06);
    }
    [data-bs-theme="dark"] .auth-feature-list li:hover {
        background: rgba(255,255,255,0.07);
        border-color: rgba(139,92,246,0.4);
    }
    .auth-feature-icon {
        width: 36px;
        height: 36px;
        border-radius: .65rem;
        background: linear-gradient(135deg, rgba(91,108,255,0.15), rgba(139,92,246,0.15));
        color: #5b6cff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }
    [data-bs-theme="dark"] .auth-feature-icon {
        background: linear-gradient(135deg, rgba(91,108,255,0.2), rgba(139,92,246,0.2));
        color: #a5b4fc;
    }
    .auth-feature-title { font-weight: 600; font-size: .9rem; line-height: 1.25; }
    .auth-feature-sub { font-size: .78rem; color: #64748b; line-height: 1.4; margin-top: 2px; }
    [data-bs-theme="dark"] .auth-feature-sub { color: #94a3b8; }

    .auth-hero-foot {
        font-size: .72rem;
        color: #64748b;
        letter-spacing: .02em;
        display: flex;
        gap: .5rem;
        align-items: center;
    }
    .auth-dot { opacity: .5; }
    [data-bs-theme="dark"] .auth-hero-foot { color: #94a3b8; }

    /* ---------- Form panel / glass card ---------- */
    .auth-panel {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        animation: cardRise .55s cubic-bezier(.2,.7,.2,1) both;
    }
    @keyframes cardRise {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .auth-card {
        position: relative;
        background: rgba(255,255,255,0.78);
        backdrop-filter: blur(28px) saturate(190%);
        -webkit-backdrop-filter: blur(28px) saturate(190%);
        border: 1px solid rgba(255,255,255,0.75);
        border-radius: 1.5rem;
        padding: clamp(1.5rem, 2.2vw, 2.25rem);
        box-shadow:
            0 30px 80px rgba(31,38,135,0.14),
            0 1px 0 rgba(255,255,255,0.7) inset,
            0 -1px 0 rgba(15,23,42,0.04) inset;
        overflow: hidden;
    }
    [data-bs-theme="dark"] .auth-card {
        background: rgba(22,27,42,0.72);
        border-color: rgba(255,255,255,0.08);
        box-shadow:
            0 30px 80px rgba(0,0,0,0.55),
            0 1px 0 rgba(255,255,255,0.06) inset,
            0 -1px 0 rgba(0,0,0,0.2) inset;
    }
    .auth-card-shine {
        position: absolute;
        top: -1px; left: 10%; right: 10%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.95), transparent);
        pointer-events: none;
    }
    [data-bs-theme="dark"] .auth-card-shine {
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
    }

    .auth-mobile-brand {
        align-items: center;
        gap: .65rem;
        margin-bottom: 1.25rem;
        text-decoration: none;
        color: inherit;
        width: fit-content;
    }

    .auth-card-header { margin-bottom: 1.5rem; }
    .auth-title {
        font-size: 1.65rem;
        font-weight: 700;
        margin: 0 0 .35rem;
        letter-spacing: -.02em;
        color: #0f172a;
    }
    .auth-subtitle {
        font-size: .9rem;
        color: #64748b;
        margin: 0;
    }
    [data-bs-theme="dark"] .auth-title { color: #f1f5f9; }
    [data-bs-theme="dark"] .auth-subtitle { color: #94a3b8; }

    /* ---------- Alert ---------- */
    .auth-alert {
        display: flex;
        align-items: center;
        gap: .65rem;
        background: rgba(239,68,68,0.08);
        border: 1px solid rgba(239,68,68,0.25);
        color: #b91c1c;
        padding: .65rem .85rem;
        border-radius: .75rem;
        font-size: .82rem;
        margin-bottom: 1rem;
    }
    .auth-alert i { font-size: 1rem; flex-shrink: 0; }
    [data-bs-theme="dark"] .auth-alert {
        background: rgba(239,68,68,0.12);
        border-color: rgba(239,68,68,0.35);
        color: #fca5a5;
    }

    /* ---------- Form ---------- */
    .auth-form { display: flex; flex-direction: column; gap: 1rem; }

    .auth-label {
        font-size: .8rem;
        font-weight: 600;
        color: #334155;
        margin: 0 0 .4rem;
        display: block;
        letter-spacing: .01em;
    }
    .auth-label-hint {
        font-size: .7rem;
        color: #94a3b8;
        font-weight: 500;
    }
    [data-bs-theme="dark"] .auth-label { color: #cbd5e1; }
    [data-bs-theme="dark"] .auth-label-hint { color: #64748b; }

    .auth-input {
        position: relative;
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(15,23,42,0.08);
        border-radius: .75rem;
        padding: 0 .25rem 0 .75rem;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        box-shadow: 0 1px 0 rgba(255,255,255,0.7) inset;
    }
    .auth-input:hover {
        border-color: rgba(91,108,255,0.3);
        background: rgba(255,255,255,0.85);
    }
    .auth-input:focus-within {
        border-color: rgba(91,108,255,0.55);
        background: #fff;
        box-shadow:
            0 0 0 4px rgba(91,108,255,0.15),
            0 1px 0 rgba(255,255,255,0.7) inset;
    }
    [data-bs-theme="dark"] .auth-input {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.08);
        box-shadow: 0 1px 0 rgba(255,255,255,0.04) inset;
    }
    [data-bs-theme="dark"] .auth-input:hover {
        background: rgba(255,255,255,0.06);
        border-color: rgba(139,92,246,0.4);
    }
    [data-bs-theme="dark"] .auth-input:focus-within {
        background: rgba(255,255,255,0.07);
        border-color: rgba(139,92,246,0.6);
        box-shadow:
            0 0 0 4px rgba(139,92,246,0.18),
            0 1px 0 rgba(255,255,255,0.05) inset;
    }
    .auth-input-icon {
        color: #94a3b8;
        font-size: 1rem;
        transition: color .2s ease;
    }
    .auth-input:focus-within .auth-input-icon { color: #5b6cff; }
    [data-bs-theme="dark"] .auth-input:focus-within .auth-input-icon { color: #a5b4fc; }

    .auth-input-control {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        padding: .75rem .65rem;
        font-size: .92rem;
        color: #0f172a;
        font-family: inherit;
    }
    .auth-input-control::placeholder { color: #94a3b8; }
    [data-bs-theme="dark"] .auth-input-control { color: #f1f5f9; }
    [data-bs-theme="dark"] .auth-input-control::placeholder { color: #64748b; }

    .auth-input-control.has-error { color: #b91c1c; }
    [data-bs-theme="dark"] .auth-input-control.has-error { color: #fca5a5; }

    .auth-input-reveal {
        background: transparent;
        border: none;
        color: #94a3b8;
        width: 36px;
        height: 36px;
        border-radius: .55rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: color .2s ease, background .2s ease;
    }
    .auth-input-reveal:hover { color: #5b6cff; background: rgba(91,108,255,0.08); }
    [data-bs-theme="dark"] .auth-input-reveal:hover { color: #a5b4fc; background: rgba(139,92,246,0.15); }

    /* ---------- Checkbox ---------- */
    .auth-row { display: flex; justify-content: space-between; align-items: center; }
    .auth-check {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        cursor: pointer;
        font-size: .85rem;
        color: #475569;
        user-select: none;
    }
    .auth-check input { position: absolute; opacity: 0; pointer-events: none; }
    .auth-check-box {
        width: 18px;
        height: 18px;
        border-radius: .35rem;
        border: 1.5px solid rgba(15,23,42,0.2);
        background: rgba(255,255,255,0.85);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: .8rem;
        transition: all .18s ease;
    }
    .auth-check-box i { opacity: 0; transform: scale(.6); transition: all .18s ease; }
    .auth-check input:checked + .auth-check-box {
        background: linear-gradient(135deg, #5b6cff, #8b5cf6);
        border-color: transparent;
        box-shadow: 0 4px 10px rgba(91,108,255,0.35);
    }
    .auth-check input:checked + .auth-check-box i { opacity: 1; transform: scale(1); }
    .auth-check input:focus-visible + .auth-check-box { box-shadow: 0 0 0 4px rgba(91,108,255,0.2); }
    [data-bs-theme="dark"] .auth-check { color: #cbd5e1; }
    [data-bs-theme="dark"] .auth-check-box {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.12);
    }

    /* ---------- Submit button ---------- */
    .auth-submit {
        position: relative;
        margin-top: .25rem;
        padding: .85rem 1.25rem;
        border-radius: .85rem;
        border: none;
        background: linear-gradient(135deg, #5b6cff 0%, #8b5cf6 100%);
        color: #fff;
        font-weight: 600;
        font-size: .95rem;
        letter-spacing: .01em;
        cursor: pointer;
        overflow: hidden;
        transition: transform .15s ease, box-shadow .25s ease, filter .2s ease;
        box-shadow:
            0 10px 28px rgba(91,108,255,0.35),
            inset 0 1px 0 rgba(255,255,255,0.25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .auth-submit .default,
    .auth-submit .loading {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
    }
    .auth-submit:hover {
        transform: translateY(-1px);
        box-shadow:
            0 14px 36px rgba(91,108,255,0.45),
            inset 0 1px 0 rgba(255,255,255,0.3);
        filter: brightness(1.04);
    }
    .auth-submit:active { transform: translateY(0); filter: brightness(.97); }
    .auth-submit[disabled] { opacity: .9; cursor: progress; transform: none; }
    .auth-submit-shine {
        position: absolute;
        top: 0; left: -75%;
        width: 60%; height: 100%;
        background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.35) 50%, transparent 100%);
        transform: skewX(-20deg);
        transition: left .6s ease;
        pointer-events: none;
    }
    .auth-submit:hover .auth-submit-shine { left: 130%; }

    /* ---------- Helper text ---------- */
    .auth-help {
        font-size: .78rem;
        color: #64748b;
        text-align: center;
        margin: .25rem 0 0;
    }
    .auth-help a {
        color: #5b6cff;
        text-decoration: none;
        font-weight: 600;
    }
    .auth-help a:hover { text-decoration: underline; }
    [data-bs-theme="dark"] .auth-help { color: #94a3b8; }
    [data-bs-theme="dark"] .auth-help a { color: #a5b4fc; }

    .auth-footer {
        text-align: center;
        font-size: .72rem;
        color: #94a3b8;
        letter-spacing: .03em;
        display: inline-flex;
        gap: .35rem;
        align-self: center;
    }

    /* ---------- Floating theme toggle ---------- */
    .auth-theme-toggle {
        position: fixed;
        top: 1.25rem;
        right: 1.25rem;
        z-index: 10;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(255,255,255,0.85);
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.1rem;
        backdrop-filter: blur(18px) saturate(180%);
        -webkit-backdrop-filter: blur(18px) saturate(180%);
        box-shadow:
            0 10px 30px rgba(31,38,135,0.18),
            inset 0 1px 0 rgba(255,255,255,0.9);
        transition: transform .25s ease, background .25s ease, color .25s ease, box-shadow .25s ease;
    }
    .auth-theme-toggle:hover {
        transform: translateY(-1px) rotate(-12deg);
        color: #5b6cff;
        background: rgba(255,255,255,0.85);
        box-shadow:
            0 14px 36px rgba(91,108,255,0.25),
            inset 0 1px 0 rgba(255,255,255,0.95);
    }
    .auth-theme-toggle:active { transform: translateY(0) rotate(0); }
    [data-bs-theme="dark"] .auth-theme-toggle {
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.1);
        color: #e2e8f0;
        box-shadow:
            0 10px 30px rgba(0,0,0,0.45),
            inset 0 1px 0 rgba(255,255,255,0.08);
    }
    [data-bs-theme="dark"] .auth-theme-toggle:hover {
        color: #fbbf24;
        background: rgba(255,255,255,0.1);
        box-shadow:
            0 14px 36px rgba(251,191,36,0.2),
            inset 0 1px 0 rgba(255,255,255,0.12);
    }

    /* ---------- Reduced motion ---------- */
    @media (prefers-reduced-motion: reduce) {
        .auth-mesh .orb,
        .auth-pill-dot,
        .auth-panel { animation: none !important; }
        .auth-submit,
        .auth-theme-toggle,
        .auth-feature-list li,
        .auth-submit-shine { transition: none !important; }
    }

    /* ---------- Small screens ---------- */
    @media (max-width: 575.98px) {
        .auth-card { padding: 1.5rem; border-radius: 1.25rem; }
        .auth-title { font-size: 1.4rem; }
        .auth-theme-toggle { top: .75rem; right: .75rem; width: 40px; height: 40px; }
    }
</style>

<script>
    (function () {
        // Password reveal
        const pw   = document.getElementById('password');
        const btn  = document.getElementById('togglePassword');
        const icon = document.getElementById('togglePasswordIcon');
        if (btn && pw && icon) {
            btn.addEventListener('click', () => {
                const isHidden = pw.type === 'password';
                pw.type = isHidden ? 'text' : 'password';
                icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
                btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            });
        }

        // Submit loading state
        const form = document.getElementById('loginForm');
        const submit = form?.querySelector('.auth-submit');
        if (form && submit) {
            form.addEventListener('submit', () => {
                submit.disabled = true;
                submit.querySelector('.default')?.classList.add('d-none');
                submit.querySelector('.loading')?.classList.remove('d-none');
            });
        }

        // Theme toggle (mirrors topbar logic)
        const root = document.documentElement;
        const tBtn = document.getElementById('authThemeToggle');
        const tIcn = document.getElementById('authThemeIcon');
        function syncIcon() {
            if (!tIcn) return;
            const dark = root.getAttribute('data-bs-theme') === 'dark';
            tIcn.className = dark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
        }
        syncIcon();
        if (tBtn) {
            tBtn.addEventListener('click', () => {
                const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                root.setAttribute('data-bs-theme', next);
                localStorage.setItem('rrs.theme', next);
                syncIcon();
            });
        }
    })();
</script>
@endsection
