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
     * Resolve the Bootstrap tone (e.g. 'success', 'danger') for a status string.
     * Unknown values fall back to 'secondary' so missing entries degrade quietly.
     */
    public static function for(?string $status): string
    {
        return self::MAP[$status ?? ''] ?? self::DEFAULT_TONE;
    }
}
