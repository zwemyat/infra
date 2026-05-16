@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $now = \Carbon\Carbon::now();
    $greeting = $now->hour < 12 ? 'Good morning' : ($now->hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $greeting }}, {{ explode(' ', $user->name)[0] }}</h1>
        <div class="page-subtitle">{{ $now->format('l, F j, Y') }} &middot; Here's what's happening across your IT assets.</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($user->canAccess('pc_assets'))
            <a href="{{ route('pc-assets.index') }}" class="quick-action"><i class="bi bi-pc-display"></i> PCs</a>
        @endif
        @if($user->canAccess('devices'))
            <a href="{{ route('devices.index') }}" class="quick-action"><i class="bi bi-hdd-network"></i> Devices</a>
        @endif
        @if($user->canAccess('subscriptions'))
            <a href="{{ route('subscriptions.index') }}" class="quick-action"><i class="bi bi-calendar-event"></i> Subscriptions</a>
        @endif
        @if($user->canAccess('licenses_contracts'))
            <a href="{{ route('licenses-contracts.index') }}" class="quick-action"><i class="bi bi-file-earmark-text"></i> Licenses</a>
        @endif
    </div>
</div>

@php
    $activeServices = (int) $stats['active_subscriptions'] + (int) $stats['active_licenses'];

    // KPI tile destinations — gated by per-module access so the overlay link only
    // renders for users who can actually open the target view.
    $pcLink     = $user->canAccess('pc_assets')     ? route('pc-assets.index') : null;
    $devLink    = $user->canAccess('devices')       ? route('devices.index')   : null;
    $subsLink   = $user->canAccess('subscriptions')
                    ? route('subscriptions.index')
                    : ($user->canAccess('licenses_contracts') ? route('licenses-contracts.index') : null);
    $expireLink = $user->canAccess('subscriptions')
                    ? route('subscriptions.index',       ['expiring_soon' => 1])
                    : ($user->canAccess('licenses_contracts')
                        ? route('licenses-contracts.index', ['expiring_soon' => 1])
                        : null);

    // First-time onboarding nudge — surfaces when an admin lands on a dashboard
    // with zero data across every module. Hides automatically once anything
    // exists; non-admins never see it (they can't create records).
    $isFirstRun = $user->isAdmin()
        && $stats['total_assets']         === 0
        && $stats['total_devices']        === 0
        && $stats['active_subscriptions'] === 0
        && $stats['active_licenses']      === 0;
@endphp

@if($isFirstRun)
<section class="onboarding-card" aria-label="Getting started">
    <div class="onboarding-head">
        <div>
            <h2 class="onboarding-title">Let's set up your IT inventory</h2>
            <p class="onboarding-sub">
                Pick a starting point below. Most teams begin with the PC fleet, then layer in subscriptions
                and licenses so renewal reminders fire on time.
            </p>
        </div>
        <span class="onboarding-badge"><i class="bi bi-rocket-takeoff"></i> Getting started</span>
    </div>
    <div class="onboarding-grid">
        <a href="{{ route('pc-assets.create') }}" class="onboarding-step" data-step="1">
            <span class="onboarding-step-num">1</span>
            <span class="onboarding-step-icon" style="--ob: 59,130,246;"><i class="bi bi-pc-display"></i></span>
            <span class="onboarding-step-body">
                <span class="onboarding-step-title">Add your first PC</span>
                <span class="onboarding-step-desc">Register workstations and laptops in the PC Master.</span>
            </span>
            <i class="bi bi-arrow-right onboarding-step-arrow"></i>
        </a>
        <a href="{{ route('devices.create') }}" class="onboarding-step" data-step="2">
            <span class="onboarding-step-num">2</span>
            <span class="onboarding-step-icon" style="--ob: 6,182,212;"><i class="bi bi-hdd-network"></i></span>
            <span class="onboarding-step-body">
                <span class="onboarding-step-title">Add a device</span>
                <span class="onboarding-step-desc">Network gear, peripherals, and other hardware.</span>
            </span>
            <i class="bi bi-arrow-right onboarding-step-arrow"></i>
        </a>
        <a href="{{ route('subscriptions.create') }}" class="onboarding-step" data-step="3">
            <span class="onboarding-step-num">3</span>
            <span class="onboarding-step-icon" style="--ob: 139,92,246;"><i class="bi bi-calendar-event"></i></span>
            <span class="onboarding-step-body">
                <span class="onboarding-step-title">Register a subscription</span>
                <span class="onboarding-step-desc">Domains, SSL, SaaS — anything with a renewal date.</span>
            </span>
            <i class="bi bi-arrow-right onboarding-step-arrow"></i>
        </a>
        <a href="{{ route('mail-settings.edit') }}" class="onboarding-step" data-step="4">
            <span class="onboarding-step-num">4</span>
            <span class="onboarding-step-icon" style="--ob: 245,158,11;"><i class="bi bi-envelope-gear"></i></span>
            <span class="onboarding-step-body">
                <span class="onboarding-step-title">Configure mail delivery</span>
                <span class="onboarding-step-desc">Set SMTP credentials and reminder recipients.</span>
            </span>
            <i class="bi bi-arrow-right onboarding-step-arrow"></i>
        </a>
    </div>
</section>
@endif
<section class="kpi-grid" aria-label="Summary metrics">
    {{-- 1. PC Assets ─ blue ─────────────────────────────────────────── --}}
    <article class="kpi-tile" data-tone="blue">
        @if($pcLink)
            <a href="{{ $pcLink }}" class="kpi-tile-link" aria-label="View all PC assets"></a>
        @endif
        <header class="kpi-tile-head">
            <span class="kpi-tile-icon"><i class="bi bi-pc-display"></i></span>
            <div class="kpi-tile-titles">
                <h3 class="kpi-tile-title">PC Assets</h3>
                <span class="kpi-tile-eyebrow">Workstation fleet</span>
            </div>
        </header>
        <div class="kpi-tile-body">
            <span class="kpi-tile-value">{{ number_format($stats['total_assets']) }}</span>
            <span class="kpi-tile-caption">Registered PCs</span>
        </div>
        <footer class="kpi-tile-foot">
            <span class="kpi-chip">
                <span class="kpi-dot kpi-dot-green" aria-hidden="true"></span>
                <span class="kpi-chip-num">{{ number_format($stats['active_assets']) }}</span>
                <span class="kpi-chip-lbl">Active</span>
            </span>
            <span class="kpi-chip">
                <span class="kpi-dot kpi-dot-blue" aria-hidden="true"></span>
                <span class="kpi-chip-num">{{ number_format($stats['free_assets']) }}</span>
                <span class="kpi-chip-lbl">Available</span>
            </span>
        </footer>
    </article>

    {{-- 2. Devices ─ blue (assets family) ───────────────────────────── --}}
    <article class="kpi-tile" data-tone="blue">
        @if($devLink)
            <a href="{{ $devLink }}" class="kpi-tile-link" aria-label="View all devices"></a>
        @endif
        <header class="kpi-tile-head">
            <span class="kpi-tile-icon"><i class="bi bi-hdd-network"></i></span>
            <div class="kpi-tile-titles">
                <h3 class="kpi-tile-title">Devices</h3>
                <span class="kpi-tile-eyebrow">Network &amp; hardware</span>
            </div>
        </header>
        <div class="kpi-tile-body">
            <span class="kpi-tile-value">{{ number_format($stats['devices_qty']) }}</span>
            <span class="kpi-tile-caption">Total units in inventory</span>
        </div>
        <footer class="kpi-tile-foot">
            <span class="kpi-chip">
                <span class="kpi-dot kpi-dot-green" aria-hidden="true"></span>
                <span class="kpi-chip-num">{{ number_format($stats['active_units']) }}</span>
                <span class="kpi-chip-lbl">Active units</span>
            </span>
            <span class="kpi-chip">
                <span class="kpi-dot kpi-dot-blue" aria-hidden="true"></span>
                <span class="kpi-chip-num">{{ number_format($stats['total_devices']) }}</span>
                <span class="kpi-chip-lbl">Records</span>
            </span>
        </footer>
    </article>

    {{-- 3. Subscriptions & Licenses ─ purple ────────────────────────── --}}
    <article class="kpi-tile" data-tone="purple">
        @if($subsLink)
            <a href="{{ $subsLink }}" class="kpi-tile-link" aria-label="View active subscriptions and licenses"></a>
        @endif
        <header class="kpi-tile-head">
            <span class="kpi-tile-icon"><i class="bi bi-stars"></i></span>
            <div class="kpi-tile-titles">
                <h3 class="kpi-tile-title">Subscriptions &amp; Licenses</h3>
                <span class="kpi-tile-eyebrow">Software services</span>
            </div>
        </header>
        <div class="kpi-tile-body">
            <span class="kpi-tile-value">{{ number_format($activeServices) }}</span>
            <span class="kpi-tile-caption">Active services</span>
        </div>
        <footer class="kpi-tile-foot">
            <span class="kpi-chip">
                <span class="kpi-dot kpi-dot-purple" aria-hidden="true"></span>
                <span class="kpi-chip-num">{{ number_format($stats['active_subscriptions']) }}</span>
                <span class="kpi-chip-lbl">Subscriptions</span>
            </span>
            <span class="kpi-chip">
                <span class="kpi-dot kpi-dot-indigo" aria-hidden="true"></span>
                <span class="kpi-chip-num">{{ number_format($stats['active_licenses']) }}</span>
                <span class="kpi-chip-lbl">Licenses</span>
            </span>
        </footer>
    </article>

    {{-- 4. Expiring Soon ─ amber/red ────────────────────────────────── --}}
    <article class="kpi-tile" data-tone="alert">
        @if($expireLink)
            <a href="{{ $expireLink }}" class="kpi-tile-link" aria-label="View items expiring within 30 days"></a>
        @endif
        <header class="kpi-tile-head">
            <span class="kpi-tile-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
            <div class="kpi-tile-titles">
                <h3 class="kpi-tile-title">Expiring Soon</h3>
                <span class="kpi-tile-eyebrow">Action required</span>
            </div>
        </header>
        <div class="kpi-tile-body">
            <span class="kpi-tile-value">{{ number_format($stats['expiring_total']) }}</span>
            <span class="kpi-tile-caption">Within 30 days</span>
        </div>
        <footer class="kpi-tile-foot">
            <span class="kpi-chip">
                <span class="kpi-dot kpi-dot-amber" aria-hidden="true"></span>
                <span class="kpi-chip-num">{{ number_format($stats['expiring_subs']) }}</span>
                <span class="kpi-chip-lbl">Subscriptions</span>
            </span>
            <span class="kpi-chip">
                <span class="kpi-dot kpi-dot-amber" aria-hidden="true"></span>
                <span class="kpi-chip-num">{{ number_format($stats['expiring_licenses']) }}</span>
                <span class="kpi-chip-lbl">Licenses</span>
            </span>
        </footer>
    </article>
</section>

<style>
    /* ─── KPI Grid ───────────────────────────────────────────────────────── */
    .kpi-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    @media (min-width: 576px)  { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1200px) { .kpi-grid { grid-template-columns: repeat(4, 1fr); } }

    /* ─── Tile shell ─────────────────────────────────────────────────────── */
    .kpi-tile {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.15rem 1.25rem;
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.07);
        border-radius: .95rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        overflow: hidden;
        isolation: isolate;
    }
    .kpi-tile::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 3px;
        background: var(--kpi-accent, #3b82f6);
        opacity: .9;
    }
    .kpi-tile::after {
        content: '';
        position: absolute;
        top: -40%;
        right: -20%;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, var(--kpi-accent-soft, rgba(59,130,246,0.10)) 0%, transparent 65%);
        z-index: -1;
        pointer-events: none;
    }
    .kpi-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        border-color: rgba(15, 23, 42, 0.12);
    }

    /* Stretched overlay link — makes the entire tile clickable when the user
       has access to the target module. Renders invisibly above the tile content. */
    .kpi-tile-link {
        position: absolute;
        inset: 0;
        z-index: 2;
        border-radius: inherit;
        cursor: pointer;
        text-decoration: none;
        /* Keyboard focus ring sits flush around the tile */
    }
    .kpi-tile-link:focus { outline: none; }
    .kpi-tile-link:focus-visible {
        outline: 2px solid var(--kpi-accent, #3b82f6);
        outline-offset: 3px;
        border-radius: calc(.95rem + 3px);
    }
    /* Lift slightly more on hover/focus when interactive */
    .kpi-tile:has(.kpi-tile-link:hover),
    .kpi-tile:has(.kpi-tile-link:focus-visible) {
        transform: translateY(-3px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.10);
    }
    [data-bs-theme="dark"] .kpi-tile:has(.kpi-tile-link:hover),
    [data-bs-theme="dark"] .kpi-tile:has(.kpi-tile-link:focus-visible) {
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45);
    }
    [data-bs-theme="dark"] .kpi-tile {
        background: rgba(30, 36, 48, 0.72);
        border-color: rgba(255, 255, 255, 0.06);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
    }
    [data-bs-theme="dark"] .kpi-tile:hover {
        border-color: rgba(255, 255, 255, 0.14);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4);
    }

    /* ─── Head ───────────────────────────────────────────────────────────── */
    .kpi-tile-head {
        display: flex;
        align-items: center;
        gap: .75rem;
    }
    .kpi-tile-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: .65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: var(--kpi-accent, #3b82f6);
        background: var(--kpi-accent-soft, rgba(59,130,246,0.12));
        box-shadow: inset 0 0 0 1px var(--kpi-accent-ring, rgba(59,130,246,0.18));
    }
    .kpi-tile-titles { display: flex; flex-direction: column; min-width: 0; line-height: 1.2; }
    .kpi-tile-title {
        font-size: .92rem;
        font-weight: 600;
        margin: 0;
        color: #0f172a;
        letter-spacing: -.005em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kpi-tile-eyebrow {
        font-size: .68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        margin-top: 2px;
    }
    [data-bs-theme="dark"] .kpi-tile-title   { color: #f1f5f9; }
    [data-bs-theme="dark"] .kpi-tile-eyebrow { color: #64748b; }

    /* ─── Body / primary metric ──────────────────────────────────────────── */
    .kpi-tile-body {
        display: flex;
        flex-direction: column;
        line-height: 1;
    }
    .kpi-tile-value {
        font-size: clamp(1.85rem, 2.4vw, 2.25rem);
        font-weight: 700;
        letter-spacing: -.02em;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
        line-height: 1.05;
    }
    .kpi-tile-caption {
        font-size: .78rem;
        color: #64748b;
        font-weight: 500;
        margin-top: .35rem;
    }
    [data-bs-theme="dark"] .kpi-tile-value   { color: #f8fafc; }
    [data-bs-theme="dark"] .kpi-tile-caption { color: #94a3b8; }

    /* ─── Foot / breakdown chips ─────────────────────────────────────────── */
    .kpi-tile-foot {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-top: .75rem;
        border-top: 1px dashed rgba(15, 23, 42, 0.08);
        flex-wrap: wrap;
    }
    [data-bs-theme="dark"] .kpi-tile-foot { border-top-color: rgba(255, 255, 255, 0.08); }

    .kpi-chip {
        display: inline-flex;
        align-items: baseline;
        gap: .4rem;
        font-size: .8rem;
        line-height: 1;
        min-width: 0;
    }
    .kpi-chip-num {
        font-weight: 700;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
        font-size: .9rem;
    }
    .kpi-chip-lbl {
        color: #64748b;
        font-weight: 500;
    }
    [data-bs-theme="dark"] .kpi-chip-num { color: #f1f5f9; }
    [data-bs-theme="dark"] .kpi-chip-lbl { color: #94a3b8; }

    .kpi-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
        align-self: center;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.04);
    }
    .kpi-dot-green  { background: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.18); }
    .kpi-dot-blue   { background: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.18); }
    .kpi-dot-purple { background: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.18); }
    .kpi-dot-indigo { background: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.18); }
    .kpi-dot-amber  { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.18); }
    .kpi-dot-red    { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.18); }

    /* ─── Tone palettes ──────────────────────────────────────────────────── */
    .kpi-tile[data-tone="blue"] {
        --kpi-accent:      #3b82f6;
        --kpi-accent-soft: rgba(59,130,246,0.12);
        --kpi-accent-ring: rgba(59,130,246,0.20);
    }
    .kpi-tile[data-tone="purple"] {
        --kpi-accent:      #8b5cf6;
        --kpi-accent-soft: rgba(139,92,246,0.12);
        --kpi-accent-ring: rgba(139,92,246,0.20);
    }
    .kpi-tile[data-tone="alert"] {
        --kpi-accent:      #f59e0b;
        --kpi-accent-soft: rgba(245,158,11,0.13);
        --kpi-accent-ring: rgba(245,158,11,0.22);
    }
    /* Dark-mode tone tweaks — slightly brighter accent for contrast */
    [data-bs-theme="dark"] .kpi-tile[data-tone="blue"]   { --kpi-accent-soft: rgba(59,130,246,0.18);  }
    [data-bs-theme="dark"] .kpi-tile[data-tone="purple"] { --kpi-accent-soft: rgba(139,92,246,0.20);  }
    [data-bs-theme="dark"] .kpi-tile[data-tone="alert"]  { --kpi-accent-soft: rgba(245,158,11,0.22);  }

    /* ─── Onboarding card (first-run admin) ──────────────────────────────── */
    .onboarding-card {
        position: relative;
        margin-bottom: 1rem;
        padding: 1.5rem;
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.07);
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        isolation: isolate;
    }
    .onboarding-card::before {
        content: '';
        position: absolute;
        top: -50%; right: -10%;
        width: 420px; height: 420px;
        border-radius: 50%;
        background: radial-gradient(circle,
            rgba(91, 108, 255, 0.10) 0%,
            rgba(139, 92, 246, 0.06) 40%,
            transparent 70%);
        z-index: -1;
        pointer-events: none;
    }
    [data-bs-theme="dark"] .onboarding-card {
        background: rgba(30, 36, 48, 0.72);
        border-color: rgba(255, 255, 255, 0.06);
    }
    [data-bs-theme="dark"] .onboarding-card::before {
        background: radial-gradient(circle,
            rgba(91, 108, 255, 0.18) 0%,
            rgba(139, 92, 246, 0.12) 40%,
            transparent 70%);
    }

    .onboarding-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }
    .onboarding-title {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: -.015em;
        margin: 0 0 .35rem;
        color: #0f172a;
    }
    .onboarding-sub {
        font-size: .85rem;
        color: #64748b;
        margin: 0;
        max-width: 60ch;
        line-height: 1.55;
    }
    [data-bs-theme="dark"] .onboarding-title { color: #f1f5f9; }
    [data-bs-theme="dark"] .onboarding-sub   { color: #94a3b8; }

    .onboarding-badge {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .7rem;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .02em;
        color: #5b6cff;
        background: rgba(91, 108, 255, 0.10);
        border: 1px solid rgba(91, 108, 255, 0.18);
        border-radius: 999px;
    }
    [data-bs-theme="dark"] .onboarding-badge {
        color: #a5b4fc;
        background: rgba(91, 108, 255, 0.18);
        border-color: rgba(91, 108, 255, 0.3);
    }

    .onboarding-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: .75rem;
    }
    @media (min-width: 768px)  { .onboarding-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1200px) { .onboarding-grid { grid-template-columns: repeat(4, 1fr); } }

    .onboarding-step {
        position: relative;
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: .85rem 1rem;
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: .75rem;
        text-decoration: none;
        color: inherit;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }
    .onboarding-step:hover,
    .onboarding-step:focus-visible {
        transform: translateY(-2px);
        background: #fff;
        border-color: rgb(var(--ob, 91 108 255) / .35);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        outline: none;
    }
    [data-bs-theme="dark"] .onboarding-step {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }
    [data-bs-theme="dark"] .onboarding-step:hover,
    [data-bs-theme="dark"] .onboarding-step:focus-visible {
        background: rgba(255, 255, 255, 0.07);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.45);
    }

    .onboarding-step-num {
        position: absolute;
        top: -8px; left: -8px;
        width: 22px; height: 22px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5b6cff 0%, #8b5cf6 100%);
        color: #fff;
        font-size: .68rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(91, 108, 255, 0.35);
    }
    .onboarding-step-icon {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: .55rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        background: rgb(var(--ob, 91 108 255) / .12);
        color: rgb(var(--ob, 91 108 255));
        box-shadow: inset 0 0 0 1px rgb(var(--ob, 91 108 255) / .18);
    }
    [data-bs-theme="dark"] .onboarding-step-icon {
        background: rgb(var(--ob, 165 180 252) / .2);
    }
    .onboarding-step-body {
        flex-grow: 1;
        min-width: 0;
        line-height: 1.3;
    }
    .onboarding-step-title {
        display: block;
        font-size: .92rem;
        font-weight: 600;
        color: #0f172a;
        letter-spacing: -.005em;
    }
    .onboarding-step-desc {
        display: block;
        font-size: .76rem;
        color: #64748b;
        margin-top: 2px;
    }
    [data-bs-theme="dark"] .onboarding-step-title { color: #f1f5f9; }
    [data-bs-theme="dark"] .onboarding-step-desc  { color: #94a3b8; }
    .onboarding-step-arrow {
        flex-shrink: 0;
        font-size: .9rem;
        color: #cbd5e1;
        transition: transform .18s ease, color .18s ease;
    }
    .onboarding-step:hover .onboarding-step-arrow,
    .onboarding-step:focus-visible .onboarding-step-arrow {
        transform: translateX(3px);
        color: rgb(var(--ob, 91 108 255));
    }

    /* ─── Reduced motion ─────────────────────────────────────────────────── */
    @media (prefers-reduced-motion: reduce) {
        .kpi-tile,
        .onboarding-step,
        .onboarding-step-arrow { transition: none; }
        .kpi-tile:hover,
        .onboarding-step:hover { transform: none; }
    }
</style>

<div class="row g-3 mb-3">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0"><i class="bi bi-bar-chart-fill text-primary"></i> Inventory Health</h6>
                    <span class="text-muted small">PC Master &amp; Device Master</span>
                </div>
                <div style="position: relative; height: 240px;">
                    <canvas id="inventoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="bi bi-clock-history text-primary"></i> Recent Activity</h6>
                    @if($user->isAdmin())
                        <a href="{{ route('activity-logs.index') }}" class="small text-decoration-none">View all</a>
                    @endif
                </div>
                <div class="flex-grow-1" style="max-height: 240px; overflow-y: auto;">
                    @forelse($recentActivity as $log)
                        @php
                            $iconMap = [
                                'created'      => ['bi-plus-circle',      'success'],
                                'updated'      => ['bi-pencil-square',    'info'],
                                'deleted'      => ['bi-trash',            'danger'],
                                'imported'     => ['bi-upload',           'warning'],
                                'renewed'      => ['bi-arrow-repeat',     'primary'],
                                'login'        => ['bi-box-arrow-in-right', 'secondary'],
                                'logout'       => ['bi-box-arrow-right',  'secondary'],
                                'login_failed' => ['bi-shield-exclamation', 'danger'],
                            ];
                            [$icon, $tone] = $iconMap[$log->action] ?? ['bi-circle-fill', 'secondary'];
                        @endphp
                        <div class="activity-item">
                            <span class="activity-icon bg-{{ $tone }}-subtle text-{{ $tone }}-emphasis"><i class="bi {{ $icon }}"></i></span>
                            <div class="activity-body">
                                <div class="text-truncate" title="{{ $log->description }}">{{ $log->description }}</div>
                                <div class="activity-meta">{{ $log->user_name ?: '—' }} &middot; {{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-4 mb-0">No activity yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-calendar-event text-primary"></i> Subscriptions Expiring (30d)</h6>
                <a href="{{ route('subscriptions.index') }}" class="small text-decoration-none">View all</a>
            </div>
            <div class="card-body p-0">
                @if($expiringSoon->isEmpty())
                    <div class="text-center text-muted py-4 small">
                        <i class="bi bi-check-circle text-success"></i> Nothing expiring in the next 30 days.
                    </div>
                @else
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Expires</th>
                                <th class="text-end">Days</th>
                                @if($user->isAdmin())<th></th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiringSoon as $sub)
                                @php
                                    $days = (int) \Carbon\Carbon::today()->diffInDays($sub->expire_date, false);
                                    $badge = $days <= 7 ? 'danger' : ($days <= 14 ? 'warning' : 'info');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 220px;" title="{{ $sub->subscription_name }}">{{ $sub->subscription_name }}</div>
                                        <div class="text-muted small">{{ $sub->service_type }} &middot; {{ $sub->project_name }}</div>
                                    </td>
                                    <td class="small">{{ $sub->expire_date->format('Y-m-d') }}</td>
                                    <td class="text-end"><span class="badge bg-{{ $badge }}-subtle text-{{ $badge }}-emphasis">{{ $days }}d</span></td>
                                    @if($user->isAdmin())
                                    <td class="text-end pe-3">
                                        <a href="{{ route('subscriptions.edit', $sub) }}" class="btn btn-sm btn-outline-primary py-0">Open</a>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-file-earmark-text text-primary"></i> Licenses Expiring (30d)</h6>
                <a href="{{ route('licenses-contracts.index') }}" class="small text-decoration-none">View all</a>
            </div>
            <div class="card-body p-0">
                @if($expiringLicenses->isEmpty())
                    <div class="text-center text-muted py-4 small">
                        <i class="bi bi-check-circle text-success"></i> Nothing expiring in the next 30 days.
                    </div>
                @else
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Software / Contract</th>
                                <th>Expires</th>
                                <th class="text-end">Days</th>
                                @if($user->isAdmin())<th></th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiringLicenses as $lic)
                                @php
                                    $days = (int) \Carbon\Carbon::today()->diffInDays($lic->expire_date, false);
                                    $badge = $days <= 7 ? 'danger' : ($days <= 14 ? 'warning' : 'info');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 220px;" title="{{ $lic->software_name }}">{{ $lic->software_name }}</div>
                                        <div class="text-muted small">{{ $lic->vendor_name ?? '—' }}</div>
                                    </td>
                                    <td class="small">{{ $lic->expire_date->format('Y-m-d') }}</td>
                                    <td class="text-end"><span class="badge bg-{{ $badge }}-subtle text-{{ $badge }}-emphasis">{{ $days }}d</span></td>
                                    @if($user->isAdmin())
                                    <td class="text-end pe-3">
                                        <a href="{{ route('licenses-contracts.edit', $lic) }}" class="btn btn-sm btn-outline-primary py-0">Open</a>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $pcStatuses     = ['Active', 'Free', 'Damage', 'Retirement', 'Low Performance'];
    $deviceStatuses = ['Active', 'Free', 'Damage', 'Retirement', 'Lost'];
    $labels = array_values(array_unique(array_merge($pcStatuses, $deviceStatuses)));
    $pcData     = array_map(fn ($s) => (int) ($assetStatusCounts[$s] ?? 0), $labels);
    $deviceData = array_map(fn ($s) => (int) ($deviceStatusCounts[$s] ?? 0), $labels);
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const ctx = document.getElementById('inventoryChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [
                    {
                        label: 'PC Master',
                        data: @json($pcData),
                        backgroundColor: 'rgba(13, 110, 253, 0.75)',
                        borderRadius: 6,
                        maxBarThickness: 36,
                    },
                    {
                        label: 'Device Master',
                        data: @json($deviceData),
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        borderRadius: 6,
                        maxBarThickness: 36,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } },
                },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, boxHeight: 12, padding: 16 } },
                    tooltip: {
                        callbacks: {
                            label: (c) => `${c.dataset.label}: ${c.parsed.y}`,
                        },
                    },
                },
            },
        });
    })();
</script>
@endpush
