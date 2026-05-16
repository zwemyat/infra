@extends('layouts.app')
@section('title', 'Edit ' . $subscription->subscription_name)
@section('content')
@include('partials._breadcrumb', ['items' => [
    ['label' => 'Dashboard',     'url' => route('dashboard')],
    ['label' => 'Subscriptions', 'url' => route('subscriptions.index')],
    ['label' => 'Edit ' . $subscription->subscription_name],
]])
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $subscription->subscription_name }}</h1>
        <div class="page-subtitle">
            <span class="badge bg-info-subtle text-info-emphasis me-1">{{ $subscription->service_type }}</span>
            @include('partials._status_badge', ['status' => $subscription->renewal_status, 'class' => 'me-1'])
            {{ $subscription->project_name }} &middot; expires {{ $subscription->expire_date->format('Y-m-d') }}
        </div>
    </div>
    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('subscriptions.update', $subscription) }}">
    @method('PUT')
    @include('subscriptions._form')
</form>
@endsection
