{{--
    Status badge — renders a "soft" Bootstrap badge using the system-wide
    status→tone map (App\Support\StatusTone). Use this everywhere a status
    string is displayed so the color palette stays consistent.

    Required:
        status  — the status string (e.g. 'Active', 'Expired')

    Optional:
        class    — extra utility classes to append (e.g. 'me-1', 'fs-7')
        showIcon — render the status-specific Bootstrap Icon glyph before
                   the label so the badge carries a non-color signal.
                   Defaults to true; pass `false` for text-only badges
                   (e.g. tight table cells where space is at a premium).
--}}
@php
    $tone = \App\Support\StatusTone::for($status ?? '');
    $extra = isset($class) && $class ? ' ' . trim($class) : '';
    $withIcon = ! (isset($showIcon) && $showIcon === false);
    $icon = $withIcon ? \App\Support\StatusTone::iconFor($status ?? '') : null;
@endphp
<span class="badge bg-{{ $tone }}-subtle text-{{ $tone }}-emphasis status-badge{{ $extra }}">
    @if($icon)<i class="bi {{ $icon }} status-badge-icon" aria-hidden="true"></i>@endif{{ $status }}</span>
