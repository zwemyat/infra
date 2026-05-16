<?php

namespace App\Support;

/**
 * Canonical status → Bootstrap "tone" mapping used by all status badges
 * (PC Assets, Devices, Subscriptions, Licenses, Search results).
 *
 * Keep this map in sync with the STATUSES constants on the relevant models.
 * Any new status value should be added here once — every consumer reads from
 * this single source so badge colors never drift between modules.
 */
class StatusTone
{
    public const MAP = [
        // Inventory + lifecycle: active / operational
        'Active'          => 'success',
        'Renewed'         => 'success',

        // Inventory: spare / unallocated
        'Free'            => 'info',

        // Inventory: needs attention (visible, not blocking)
        'Low Performance' => 'warning',
        'Lost'            => 'warning',

        // Lifecycle: waiting on action
        'Pending'         => 'warning',

        // Inventory + lifecycle: failed / blocking
        'Damage'          => 'danger',
        'Expired'         => 'danger',

        // Retired / closed states (neutral, no action required)
        'Retirement'      => 'secondary',
        'Cancelled'       => 'secondary',
        'Terminated'      => 'secondary',
    ];

    public const DEFAULT_TONE = 'secondary';

    /**
     * Bootstrap Icons class per status. Used by the status-badge partial so
     * each badge carries a non-color affordance (a meaningful glyph) in
     * addition to its tone. Keeps colorblind users informed when status
     * tone alone would be ambiguous.
     */
    public const ICON_MAP = [
        'Active'          => 'bi-check2-circle',
        'Renewed'         => 'bi-arrow-clockwise',
        'Free'            => 'bi-inbox',
        'Pending'         => 'bi-hourglass-split',
        'Low Performance' => 'bi-exclamation-triangle',
        'Lost'            => 'bi-question-circle',
        'Damage'          => 'bi-x-octagon',
        'Expired'         => 'bi-calendar-x',
        'Retirement'      => 'bi-archive',
        'Cancelled'       => 'bi-x-circle',
        'Terminated'      => 'bi-slash-circle',
    ];

    public const DEFAULT_ICON = 'bi-circle';

    /**
     * Resolve the Bootstrap tone (e.g. 'success', 'danger') for a status string.
     * Unknown values fall back to 'secondary' so missing entries degrade quietly.
     */
    public static function for(?string $status): string
    {
        return self::MAP[$status ?? ''] ?? self::DEFAULT_TONE;
    }

    /**
     * Resolve the Bootstrap Icons class (e.g. 'bi-check2-circle') for a status.
     * Unknown values fall back to 'bi-circle'.
     */
    public static function iconFor(?string $status): string
    {
        return self::ICON_MAP[$status ?? ''] ?? self::DEFAULT_ICON;
    }
}
