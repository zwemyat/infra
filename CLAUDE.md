# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

**Renewal Reminding System (RRS) / ITAMS** — Laravel 11 IT asset management app for tracking PCs, subscriptions, license/contracts, and miscellaneous devices, with daily expiration reminders by email. See `Renewal_Reminding_System 2.md` for the original spec.

Stack: PHP 8.2+, Laravel 11, Blade + Bootstrap 5 (CDN), MySQL/MariaDB (via XAMPP on Windows), `maatwebsite/excel` for import/export. No build tooling — there is no Node, no Vite, no Tailwind. CSS and JS live inline in the Blade layout and per-page; Bootstrap CSS/JS/Icons and Chart.js are pulled from `cdn.jsdelivr.net`.

## Environment & Commands

Local stack runs on XAMPP. PHP and MySQL are NOT on `PATH` — invoke them by absolute path. The DB is named `itams` (the older `rrs_system` name appears in some legacy comments — current `.env` uses `itams`).

```powershell
# Run dev server
& D:\xampp\php\php.exe D:\xampp\htdocs\itams\artisan serve --host=127.0.0.1 --port=8000

# Migrate / seed
& D:\xampp\php\php.exe D:\xampp\htdocs\itams\artisan migrate --force
& D:\xampp\php\php.exe D:\xampp\htdocs\itams\artisan db:seed --force

# Clear caches after view/route/config/Blade-directive changes
& D:\xampp\php\php.exe D:\xampp\htdocs\itams\artisan optimize:clear

# Run the daily expiration job manually (also scheduled at 09:00 in routes/console.php)
& D:\xampp\php\php.exe D:\xampp\htdocs\itams\artisan app:check-expirations

# MySQL CLI for ad-hoc queries
& D:\xampp\mysql\bin\mysql.exe -u root -D itams -e "SELECT ..."
```

Tests use PHPUnit 11 (`vendor/bin/phpunit` or `php artisan test`). `tests/` only contains skeleton `ExampleTest.php` files — there is no real suite yet, so don't claim "tests pass" as evidence of feature correctness.

Seeded credentials: `admin@rrs.local` / `password` (admin) and `user@rrs.local` / `password` (standard user).

## Architecture

### Modular access control

Four user-facing modules — `pc_assets`, `subscriptions`, `licenses_contracts`, `devices` — each gated by **two** middleware layers in `routes/web.php`:

1. `module:<name>` (`EnsureUserCanAccessModule`) — checks `User::canAccess()`, which is `true` for admins or when the matching `can_<module>` flag is set on the user.
2. `admin` (`EnsureUserIsAdmin`) — wraps **write routes** (store/update/destroy, bulk-destroy, import, custom actions like `subscriptions.renew`). Non-admin module users get read-only access.

`User::MODULES` (`app/Models/User.php`) is the canonical module list — keep it in sync with the `can_*` columns and the middleware aliases registered in `bootstrap/app.php`.

**Route ordering gotcha** (already documented inline at `routes/web.php:30`): inside each module group, custom routes like `pc-assets/export` and the admin-only `Route::resource(...)->except(['index','show'])` must come **before** the public `->only(['index','show'])` resource. Otherwise `GET /pc-assets/create` matches the `show` route with `pc_asset='create'` and 404s. Follow the same pattern when adding routes.

### Reminder pipeline

`Subscription` is the only model with auto-computed reminder behavior:

- `Subscription::booted()` sets `reminder_date = expire_date - reminder_days_before` on every save. `reminder_days_before` is read from the singleton `MailSetting` row (defaults to 30; falls back gracefully if `mail_settings` doesn't exist yet during initial migrate).
- `app:check-expirations` (`app/Console/Commands/CheckExpirations.php`, scheduled daily at 09:00 in `routes/console.php`):
  1. Flips active subscriptions past `expire_date` to `renewal_status = 'Expired'` (via `saveQuietly()` to skip the `booted` recomputation).
  2. Flips active subscriptions within the reminder window to `'Pending'`, creates one `Notification` row per subscription per day (deduped on `created_at` date), and emails recipients.
  3. Recipients come from `MailSetting::recipientsArray()` (comma/semicolon/whitespace-separated emails), falling back to all admin users' emails when empty.

The scheduler is driven by Windows Task Scheduler invoking `php artisan schedule:run` every minute (per the spec).

`LicenseContract` does NOT participate in this pipeline — it has expire dates but no reminder/notification logic. Treat it as data-only for now.

### Activity logging

Every state-changing controller action calls `App\Support\ActivityLogger::log(...)` to write to `activity_logs`. Login/logout/failed-login are logged from `Auth\LoginController`. When adding new mutating endpoints, follow the existing pattern: `action` is a short verb (`created`/`updated`/`deleted`/`imported`/`renewed`/`login`/`logout`/`login_failed`), `subject` is the model when applicable, and `properties` carries structured context (e.g. `['changed_fields' => [...]]`). Failed logins use the `overrides` arg because `Auth::user()` is null.

### Encrypted columns

`PcAsset` casts `admin_password`, `username`, `password` as `encrypted`; `MailSetting` casts `password` the same way. On updates, controllers explicitly `unset()` empty password fields so blank form submissions don't wipe stored credentials (see `PcAssetController::update`). Preserve this pattern when adding new encrypted fields.

### Import/export

Each module has paired `App\Exports\*Export` + `*Template` + `App\Imports\*Import` classes using `maatwebsite/excel`. Imports implement `WithHeadingRow`, `WithValidation`, `SkipsOnFailure`, and deduplicate against existing rows in-memory before insert (see `PcAssetsImport::existingIds()`). When adding a new import, mirror this pattern — collect failures, count skipped duplicates, and surface both back via flash messages in the controller.

### Shared view conventions

The UI is fully server-rendered Blade. A small set of reusable building blocks lives in `resources/views/partials/` and `app/Support/`:

- **Status badges** — every place that renders a status string (Active, Free, Damage, Pending, Expired, etc.) uses `@include('partials._status_badge', ['status' => $value])`. The badge tone (Bootstrap color) AND icon glyph both come from `App\Support\StatusTone::for($status)` and `::iconFor($status)`. **Add new status values to `StatusTone::MAP` and `::ICON_MAP`, not to per-view inline maps.**
- **Breadcrumb** — `@include('partials._breadcrumb', ['items' => [...]])` on every create/edit/show page. Item format: `['label' => '...', 'url' => '...']` for links, `['label' => '...']` for the current page. Already wired into all module create/edit/show + settings pages.
- **Brand logo** — `@include('partials._brand_logo')` renders the white SVG glyph designed to sit inside the gradient `.brand-mark` / `.auth-brand-mark` wrappers (sidebar header + login page).
- **Sort header / per-page selector** — `_sort_header.blade.php` + `_per_page.blade.php` for table column sorting and page-size dropdowns. Backed by `App\Support\Sortable`.

### Form field a11y convention

Every input that has an `@error('field')` block should also have:

1. `id="{field}"` on the input (or `id="{field}_{key}"` if multiple forms with the same field name appear on one page — see `notification_settings/edit.blade.php`)
2. `for="{field}"` on its `<label>`
3. `@aria('{field}')` on the input — Blade directive registered in `AppServiceProvider` that emits `aria-invalid="true" aria-describedby="{field}-error"` only when validation has failed for that field
4. `id="{field}-error"` on the matching `.invalid-feedback` element

This is the existing pattern across all 8 forms (login, PC asset, device, subscription, license, user, mail settings, notification settings). The `@aria` directive lives in `AppServiceProvider::registerBladeDirectives()`.

### Dashboard query pattern

`DashboardController::index` issues **exactly 10 queries** — including two `GROUP BY status` queries that double-duty: they feed both the KPI tile numbers AND the inventory chart's per-status counts. Do NOT re-add separate `COUNT()` queries for total/active/free etc. — derive them from `array_sum()` and key access on the GROUP BY result (see the controller for the pattern). `Device` stats include both `COUNT(*)` and `COALESCE(SUM(qty), 0)` in one query to surface "records" and "units" separately.

The dashboard view also uses a first-run onboarding banner (`$isFirstRun` flag) that only renders when the user is admin AND every module total is zero. It hides automatically once anything exists.

### Frontend conventions

- **No build step.** All CSS sits in `<style>` blocks inside `resources/views/layouts/app.blade.php` (global) or page Blade files (page-specific). All JS is inline `<script>` blocks or CDN imports. Don't add `@vite()` directives or reintroduce Tailwind utilities.
- **Tooltips.** Native `title=""` attributes are auto-upgraded to Bootstrap tooltips by `window.initTooltips(root)` defined in the layout. After any AJAX DOM swap (see `swap()` functions in the four index pages), call `window.initTooltips(fresh)` so re-rendered rows keep their tooltips.
- **Skip link.** The first focusable element on every authenticated page is `<a class="skip-link" href="#main-content">` (defined in `layouts/app.blade.php`). The `<div class="content" id="main-content" tabindex="-1">` is its target. Keep the `tabindex="-1"` so the anchor jump can deliver programmatic focus.
- **KPI tiles are direct anchors**, not stretched-link overlays. Each `<a class="kpi-tile">` wraps the entire tile when the user has access; falls back to `<article class="kpi-tile">` for non-accessible modules (see `dashboard.blade.php`). Don't reintroduce inner overlay links — they fight the tile's `isolation: isolate` stacking context.
- **Bulk selection is page-local.** The toolbar copy on all four list pages reads "X selected on this page · Y match this filter". Preserve this wording when changing bulk-action UX; selecting "all" never spans pages.
- **Chart frames use clamp-based heights** (`.chart-frame`, `.chart-frame-sm`, `.activity-scroll`) so they stay readable on phones and don't blow up on big monitors. Don't reintroduce fixed pixel heights.

## Notes for editing

- This is a Windows/XAMPP project. Don't assume Unix tooling; commands above invoke binaries by absolute path. The previous working directory was `C:\xampp\htdocs\infra-ninja`; references to it in `.claude/settings.local.json` permission entries are stale paths from an earlier rename and can be ignored.
- `APP_TIMEZONE` defaults to `UTC` in `.env.example`. The daily 09:00 schedule fires in whatever timezone `config('app.timezone')` resolves to in your `.env` — verify before relying on local time.
- `routes/web.php` is the single routing file; there is no `api.php`. The app is fully server-rendered Blade, no SPA layer.
- After touching `AppServiceProvider` (e.g. adding a Blade directive) or any Blade views, run `php artisan optimize:clear` — view caches especially can hold stale compiled output.
