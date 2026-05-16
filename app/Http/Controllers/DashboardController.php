<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Device;
use App\Models\LicenseContract;
use App\Models\Notification;
use App\Models\PcAsset;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $in30Days = $today->copy()->addDays(30);

        // ── PC Assets ───────────────────────────────────────────────
        // Single GROUP BY query feeds both the KPI tile and the chart.
        $assetStatusCounts = PcAsset::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        // ── Devices ─────────────────────────────────────────────────
        // Single GROUP BY pulls record counts AND unit sums per status —
        // covers the KPI tile (total units, active units, total records)
        // and the inventory chart.
        $deviceStats = Device::selectRaw('status, COUNT(*) as records, COALESCE(SUM(qty), 0) as units')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $deviceStatusCounts = $deviceStats->pluck('records', 'status')->all();

        // ── Subscriptions / Licenses (lifecycle, not GROUP-BY-able) ──
        // Active counts and "expiring within 30 days" counts use distinct
        // WHERE filters, so they stay as targeted COUNT queries.
        $activeSubs = Subscription::where('status', 'Active')->count();
        $expiringSubsCount = Subscription::where('status', 'Active')
            ->where('renewal_status', '!=', 'Renewed')
            ->whereBetween('expire_date', [$today, $in30Days])
            ->count();

        $activeLicenses = LicenseContract::where('status', 'Active')->count();
        $expiringLicensesCount = LicenseContract::whereNotIn('status', ['Terminated', 'Expired'])
            ->whereBetween('expire_date', [$today, $in30Days])
            ->count();

        $stats = [
            'total_assets'         => (int) array_sum($assetStatusCounts),
            'active_assets'        => (int) ($assetStatusCounts['Active'] ?? 0),
            'free_assets'          => (int) ($assetStatusCounts['Free']   ?? 0),

            'total_devices'        => (int) $deviceStats->sum('records'),
            'devices_qty'          => (int) $deviceStats->sum('units'),
            'active_devices'       => (int) ($deviceStats['Active']->records ?? 0),
            'active_units'         => (int) ($deviceStats['Active']->units   ?? 0),

            'active_subscriptions' => $activeSubs,
            'active_licenses'      => $activeLicenses,

            'expiring_subs'        => $expiringSubsCount,
            'expiring_licenses'    => $expiringLicensesCount,
            'expiring_total'       => $expiringSubsCount + $expiringLicensesCount,
        ];

        // ── Side panels ─────────────────────────────────────────────
        $expiringSoon = Subscription::where('status', 'Active')
            ->where('renewal_status', '!=', 'Renewed')
            ->whereBetween('expire_date', [$today, $in30Days])
            ->orderBy('expire_date')
            ->limit(6)
            ->get();

        $expiringLicenses = LicenseContract::whereNotIn('status', ['Terminated', 'Expired'])
            ->whereBetween('expire_date', [$today, $in30Days])
            ->orderBy('expire_date')
            ->limit(6)
            ->get();

        $unreadNotifications = Notification::whereNull('read_at')->count();

        $recentActivity = ActivityLog::orderByDesc('created_at')->limit(8)->get();

        return view('dashboard', compact(
            'stats',
            'assetStatusCounts',
            'deviceStatusCounts',
            'expiringSoon',
            'expiringLicenses',
            'unreadNotifications',
            'recentActivity',
        ));
    }
}
