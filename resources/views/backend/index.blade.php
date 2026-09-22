@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page_title', 'Dashboard')

@section('content')
@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif
@if(session('error'))
    @include('partials.ui.alert', ['variant' => 'danger', 'message' => session('error')])
@endif

{{-- Primary KPIs — existing variables only --}}
<div class="row g-3 mb-4 row-cols-2 row-cols-md-3 row-cols-xl-5">
    <div class="col">
        <div class="admin-kpi">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="admin-kpi-label mb-0">Total users</div>
                <span class="rounded-3 bg-primary bg-opacity-10 text-primary p-2"><i class="bi bi-people"></i></span>
            </div>
            <div class="admin-kpi-value">{{ number_format($totalUsers) }}</div>
            <div class="small text-muted mt-1">All time</div>
        </div>
    </div>
    <div class="col">
        <div class="admin-kpi">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="admin-kpi-label mb-0">Total drivers</div>
                <span class="rounded-3 bg-info bg-opacity-10 text-info p-2"><i class="bi bi-person-badge"></i></span>
            </div>
            <div class="admin-kpi-value">{{ number_format($totalDrivers) }}</div>
            <div class="small text-success mt-1">{{ $approvedDrivers }} approved</div>
        </div>
    </div>
    <div class="col">
        <div class="admin-kpi">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="admin-kpi-label mb-0">Branches</div>
                <span class="rounded-3 bg-brand bg-opacity-25 text-dark p-2"><i class="bi bi-geo-alt"></i></span>
            </div>
            <div class="admin-kpi-value">{{ number_format($totalBranches) }}</div>
            <div class="small text-muted mt-1">Active locations</div>
        </div>
    </div>
    <div class="col">
        <div class="admin-kpi">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="admin-kpi-label mb-0">Bookings</div>
                <span class="rounded-3 bg-success bg-opacity-10 text-success p-2"><i class="bi bi-calendar-check"></i></span>
            </div>
            <div class="admin-kpi-value text-success">{{ number_format($totalBookings) }}</div>
            <div class="small text-muted mt-1">Active rides: {{ $activeRidesCount }}</div>
        </div>
    </div>
    <div class="col">
        <div class="admin-kpi">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="admin-kpi-label mb-0">Revenue</div>
                <span class="rounded-3 bg-warning bg-opacity-10 text-warning p-2"><i class="bi bi-currency-rupee"></i></span>
            </div>
            <div class="admin-kpi-value">{{ $default_currency?->symbol ?? '₹' }}{{ number_format($totalRevenue) }}</div>
            <div class="small text-muted mt-1">Lifetime earnings</div>
        </div>
    </div>
</div>

{{-- Action required --}}
<div class="admin-card mb-4 p-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <div class="fw-bold">Action required</div>
            <div class="small text-muted">Jump to common operational queues.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.drivers.kyc.pending') }}" class="btn btn-sm btn-outline-dark rounded-pill">Pending KYC</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-dark rounded-pill">Pending bookings</a>
            <a href="{{ route('admin.finance.transactions') }}" class="btn btn-sm btn-dark rounded-pill">Transactions</a>
        </div>
    </div>
</div>

{{-- Charts (existing Chart.js + data) --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Revenue analytics (last 7 days)</h2>
            </div>
            <div class="p-3" style="min-height: 280px;">
                <canvas id="revenueChart" height="250" aria-label="Revenue chart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Bookings trend (last 7 days)</h2>
            </div>
            <div class="p-3" style="min-height: 280px;">
                <canvas id="bookingsChart" height="250" aria-label="Bookings chart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-2">
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Active rides</h2>
                <span class="badge bg-primary rounded-pill">{{ $activeRidesCount }} live</span>
            </div>
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle mb-0 admin-responsive-table">
                    <thead>
                        <tr>
                            <th class="px-4">Ride ID</th>
                            <th>User & driver</th>
                            <th>Route</th>
                            <th class="text-end px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($activeRides) && count($activeRides) > 0)
                            @foreach($activeRides as $ride)
                        <tr>
                            <td class="px-4 fw-bold">#{{ $ride->id }}</td>
                            <td>
                                <div class="small fw-bold">{{ $ride->user_name }} (User)</div>
                                <div class="small text-muted">{{ $ride->driver_name }} (Driver)</div>
                            </td>
                            <td>
                                <div class="small fw-bold text-success"><i class="bi bi-geo-alt-fill"></i> {{ $ride->pickup }}</div>
                                <div class="small text-danger"><i class="bi bi-geo-alt-fill"></i> {{ $ride->dropoff }}</div>
                            </td>
                            <td class="text-end px-4">
                                @include('partials.ui.status-badge', ['label' => $ride->status, 'variant' => 'info'])
                            </td>
                        </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="p-4">
                                @include('partials.ui.empty-state', [
                                    'title' => 'No active rides',
                                    'message' => 'Live trips will appear here when drivers are on the road.',
                                    'icon' => 'bi-map',
                                ])
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h2 class="admin-card-title">Recent drivers</h2>
                <a href="{{ route('admin.drivers.index') }}" class="btn btn-sm btn-link text-decoration-none fw-bold">View all</a>
            </div>
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle mb-0 admin-responsive-table">
                    <thead>
                        <tr>
                            <th class="px-4">Driver</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th class="text-end px-4">Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($recentDrivers) && count($recentDrivers) > 0)
                            @foreach($recentDrivers as $driver)
                        <tr>
                            <td class="px-4 fw-bold">{{ $driver->name }}</td>
                            <td>{{ $driver->mobile }}</td>
                            <td>
                                @include('partials.ui.status-badge', [
                                    'label' => $driver->status == 'approved' ? 'Approved' : ($driver->status == 'pending' ? 'Pending KYC' : 'Rejected'),
                                    'variant' => $driver->status == 'approved' ? 'success' : ($driver->status == 'pending' ? 'warning' : 'danger'),
                                ])
                            </td>
                            <td class="text-end px-4 small text-muted">{{ $driver->created_at->diffForHumans() }}</td>
                        </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="p-4">
                                @include('partials.ui.empty-state', [
                                    'title' => 'No registrations yet',
                                    'message' => 'New driver sign-ups will show here.',
                                    'icon' => 'bi-person-badge',
                                ])
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof Chart === 'undefined') return;
        var revenueEl = document.getElementById('revenueChart');
        var bookingsEl = document.getElementById('bookingsChart');
        if (!revenueEl || !bookingsEl) return;

        var ctxRevenue = revenueEl.getContext('2d');
        var revenueChart = new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueChartData['labels']) !!},
                datasets: [{
                    label: 'Revenue ({{ $default_currency?->symbol ?? '₹' }})',
                    data: {!! json_encode($revenueChartData['data']) !!},
                    borderColor: '#3d4508',
                    backgroundColor: 'rgba(205, 220, 41, 0.25)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });

        var ctxBookings = document.getElementById('bookingsChart').getContext('2d');
        var bookingsChart = new Chart(ctxBookings, {
            type: 'bar',
            data: {
                labels: {!! json_encode($bookingsChartData['labels']) !!},
                datasets: [{
                    label: 'Bookings',
                    data: {!! json_encode($bookingsChartData['data']) !!},
                    backgroundColor: '#198754',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection
