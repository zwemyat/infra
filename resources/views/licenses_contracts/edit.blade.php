@extends('layouts.app')
@section('title', 'Edit ' . $item->software_name)
@section('content')
@include('partials._breadcrumb', ['items' => [
    ['label' => 'Dashboard',          'url' => route('dashboard')],
    ['label' => 'License & Contract', 'url' => route('licenses-contracts.index')],
    ['label' => 'Edit ' . $item->software_name],
]])
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $item->software_name }}</h1>
        <div class="page-subtitle">
            @include('partials._status_badge', ['status' => $item->status, 'class' => 'me-1'])
            @if($item->vendor_name) {{ $item->vendor_name }} &middot; @endif
            expires {{ $item->expire_date->format('Y-m-d') }}
        </div>
    </div>
    <a href="{{ route('licenses-contracts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('licenses-contracts.update', $item) }}">
    @method('PUT')
    @include('licenses_contracts._form')
</form>
@endsection
