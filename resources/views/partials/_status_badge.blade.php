{{--
    Status badge — renders a "soft" Bootstrap badge using the system-wide
    status→tone map (App\Support\StatusTone). Use this everywhere a status
    string is displayed so the color palette stays consistent.

    Required:
        status  — the status string (e.g. 'Active', 'Expired')

    Optional:
        class   — extra utility classes to append (e.g. 'me-1', 'fs-7')
--}}
@php
    $tone = \App\Support\StatusTone::for($status ?? '');
    $extra = isset($class) && $class ? ' ' . trim($class) : '';
@endphp
<span class="badge bg-{{ $tone }}-subtle text-{{ $tone }}-emphasis{{ $extra }}">{{ $status }}</span>
