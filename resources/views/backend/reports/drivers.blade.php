@extends('layouts.admin')

@section('title', 'Driver Performance Report')
@section('page_title', 'Driver Performance Tracking')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-vcard text-brand me-2"></i> Driver Metrics</h5>
    <a href="{{ route('admin.reports.drivers', ['export' => 'csv']) }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-file-earmark-excel me-1"></i> Export Data (CSV)
    </a>
</div>

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Driver Name</th>
                        <th class="py-3 text-muted small fw-bold border-0">Reg. Date</th>
                        <th class="py-3 text-muted small fw-bold border-0">Service Area</th>
                        <th class="py-3 text-muted small fw-bold border-0">Trips Completed</th>
                        <th class="py-3 text-muted small fw-bold border-0">Rating & Status</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($drivers) && count($drivers) > 0)
                        @foreach($drivers as $driver)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $driver->name }}</div>
                                        <div class="text-muted small">ID: #{{ str_pad($driver->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold small">{{ $driver->created_at->format('M d, Y') }}</div>
                                <div class="text-muted small">{{ $driver->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                @if($driver->city)
                                    <span class="badge bg-light text-secondary"><i class="bi bi-geo-alt-fill text-muted me-1"></i>{{ $driver->city }}</span>
                                @else
                                    <span class="text-muted small">Not specified</span>
                                @endif
                            </td>
                            <!-- Simulated Metric Data -->
                            <td class="fw-bold text-dark">{{ rand(10, 500) }} <span class="text-muted fw-normal small">Trips</span></td>
                            <td>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-star-fill text-warning me-1 small"></i>
                                    <span class="fw-bold small">{{ number_format(rand(35, 50) / 10, 1) }}</span>
                                </div>
                                @if($driver->status == 'approved')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> Active</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1"><i class="bi bi-clock-fill me-1"></i> Pending</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.drivers.view', $driver->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">View Profile</a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-car-front text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No active driver data available for reporting.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $drivers->links() }}
        </div>
    </div>
</div>
@endsection
