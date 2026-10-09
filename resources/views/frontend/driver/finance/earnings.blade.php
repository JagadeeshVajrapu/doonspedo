@extends('layouts.driver')

@section('title', 'My Earnings - Doonspedo')

@section('content')
@php
    $sym = $driverCurrency?->symbol ?? '₹';
    $rate = $driverCurrency?->exchange_rate ?? 1.0;
@endphp

<div class="drv-page-header">
    <div>
        <h1>Earnings</h1>
        <p class="text-muted mb-0 small">Track your income across different time periods.</p>
    </div>
    <div class="text-md-end">
        <div class="h3 fw-bold mb-0 text-success">{{ $sym }}{{ number_format($stats['total']['net'] * $rate, 2) }}</div>
        <div class="small text-muted fw-bold text-uppercase">Lifetime net earnings</div>
        <div class="extra-small text-muted">Gross: {{ $sym }}{{ number_format($stats['total']['gross'] * $rate, 2) }}</div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="drv-stat border-bottom border-4 border-success">
            <div class="drv-stat-label">Today</div>
            <div class="drv-stat-value">{{ $sym }}{{ number_format($stats['today']['net'] * $rate, 2) }}</div>
            <p class="small text-muted mb-0 mt-1">Gross: {{ $sym }}{{ number_format($stats['today']['gross'] * $rate, 2) }}</p>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="drv-stat border-bottom border-4" style="border-color: #cddc29 !important;">
            <div class="drv-stat-label">This week</div>
            <div class="drv-stat-value">{{ $sym }}{{ number_format($stats['week']['net'] * $rate, 2) }}</div>
            <p class="small text-muted mb-0 mt-1">Gross: {{ $sym }}{{ number_format($stats['week']['gross'] * $rate, 2) }}</p>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="drv-stat border-bottom border-4 border-info">
            <div class="drv-stat-label">This month</div>
            <div class="drv-stat-value">{{ $sym }}{{ number_format($stats['month']['net'] * $rate, 2) }}</div>
            <p class="small text-muted mb-0 mt-1">Gross: {{ $sym }}{{ number_format($stats['month']['gross'] * $rate, 2) }}</p>
        </div>
    </div>
</div>

<div class="drv-card overflow-hidden">
    <div class="px-4 py-3 border-bottom">
        <h2 class="h5 fw-bold mb-0">Recent completed trips</h2>
    </div>

    <div class="d-none d-lg-block table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="small text-uppercase fw-bold text-muted">
                    <th class="px-4 py-3">Ride details</th>
                    <th class="py-3">Date & time</th>
                    <th class="py-3">Distance</th>
                    <th class="py-3">Gross fare</th>
                    <th class="py-3">Commission ({{$commissionRate}}%)</th>
                    <th class="px-4 py-3 text-end">Net earning</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($bookings) && count($bookings) > 0)
                    @foreach($bookings as $ride)
                <tr>
                    <td class="px-4">
                        <div class="fw-bold text-dark">{{ $ride->displayReference() }}</div>
                        <div class="small text-muted">{{ $ride->service_type }}</div>
                    </td>
                    <td>
                        @php $completed = $ride->completed_at ?? $ride->created_at; @endphp
                        <div class="small fw-bold">{{ $completed ? $completed->format('d M, Y') : '—' }}</div>
                        <div class="extra-small text-muted">{{ $completed ? $completed->format('h:i A') : '' }}</div>
                    </td>
                    <td>
                        <div class="small">{{ $ride->distance }} km</div>
                    </td>
                     <td>
                        <div class="small fw-bold">{{ $sym }}{{ number_format($ride->fare * $rate, 2) }}</div>
                    </td>
                    <td>
                        @php
                            $commission = $ride->commission_amount > 0 ? $ride->commission_amount : (($commissionRate / 100) * $ride->fare);
                        @endphp
                        <div class="small text-danger">-{{ $sym }}{{ number_format($commission * $rate, 2) }}</div>
                    </td>
                    <td class="px-4 text-end">
                        <div class="h6 fw-bold text-success mb-0">{{ $sym }}{{ number_format(($ride->net_amount > 0 ? $ride->net_amount : ($ride->fare - $commission)) * $rate, 2) }}</div>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="py-4">
                        @include('partials.ui.empty-state', [
                            'title' => 'No completed trips',
                            'message' => 'Start accepting rides to see your earnings here.',
                            'icon' => 'bi-graph-up-arrow',
                        ])
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    <div class="d-lg-none p-3">
        @if(isset($bookings) && count($bookings) > 0)
            @foreach($bookings as $ride)
            @php
                $commission = $ride->commission_amount > 0 ? $ride->commission_amount : (($commissionRate / 100) * $ride->fare);
            @endphp
            <div class="border rounded-4 p-3 mb-2">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        @php $completed = $ride->completed_at ?? $ride->created_at; @endphp
                        <div class="fw-bold">{{ $ride->displayReference() }}</div>
                        <div class="extra-small text-muted">{{ $ride->service_type }} · {{ $completed ? $completed->format('d M, Y') : '—' }}</div>
                    </div>
                    <div class="fw-bold text-success">{{ $sym }}{{ number_format(($ride->net_amount > 0 ? $ride->net_amount : ($ride->fare - $commission)) * $rate, 2) }}</div>
                </div>
                <div class="small text-muted d-flex justify-content-between">
                    <span>{{ $ride->distance }} km</span>
                    <span>Gross {{ $sym }}{{ number_format($ride->fare * $rate, 2) }}</span>
                </div>
            </div>
            @endforeach
        @else
            @include('partials.ui.empty-state', [
                'title' => 'No completed trips',
                'message' => 'Start accepting rides to see your earnings here.',
                'icon' => 'bi-graph-up-arrow',
            ])
        @endif
    </div>
</div>

<style>
.bg-brand { background-color: #cddc29 !important; }
.text-brand { color: #cddc29 !important; }
.extra-small { font-size: 0.65rem; }
</style>
@endsection
