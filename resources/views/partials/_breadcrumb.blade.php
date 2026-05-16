{{--
    Breadcrumb trail rendered above the page-header on deep pages.
    Usage:
        @include('partials._breadcrumb', ['items' => [
            ['label' => 'Dashboard',  'url' => route('dashboard')],
            ['label' => 'PC Master',  'url' => route('pc-assets.index')],
            ['label' => 'Edit PC-001'],   // last item: current page (no url)
        ]])

    The last item is rendered as plain text (the current page).
--}}
@php $items = $items ?? []; @endphp
@if(!empty($items))
<nav class="app-breadcrumb" aria-label="breadcrumb">
    <ol class="app-breadcrumb-list">
        @foreach($items as $i => $item)
            @php
                $isLast = $i === count($items) - 1;
                $label = $item['label'] ?? '';
                $url = $item['url'] ?? null;
            @endphp
            <li class="app-breadcrumb-item {{ $isLast ? 'is-current' : '' }}" @if($isLast) aria-current="page" @endif>
                @if($url && !$isLast)
                    <a href="{{ $url }}">{{ $label }}</a>
                @else
                    <span>{{ $label }}</span>
                @endif
                @if(!$isLast)
                    <i class="bi bi-chevron-right app-breadcrumb-sep" aria-hidden="true"></i>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
