@extends('layouts.admin')

@section('title', 'Manage Bids')
@section('page_title', 'Bidding Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-hammer text-brand me-2"></i> All Bids</h5>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <!-- Filter Form -->
        <form action="{{ route('admin.bids.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Status Filter</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold me-2"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ route('admin.bids.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Reset</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Bid ID</th>
                        <th class="py-3 text-muted small fw-bold border-0">Booking Info</th>
                        <th class="py-3 text-muted small fw-bold border-0">Driver</th>
                        <th class="py-3 text-muted small fw-bold border-0">Bid Amount</th>
                        <th class="py-3 text-muted small fw-bold border-0">Status</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($bids) && count($bids) > 0)
                        @foreach($bids as $bid)
                        <tr>
                            <td class="fw-bold">#{{ $bid->id }}</td>
                            <td>
                                @if($bid->booking)
                                    <div>Booking #{{ $bid->booking->id }}</div>
                                    <div class="small text-muted">{{ ucfirst($bid->booking->service_type) }}</div>
                                @else
                                    <span class="text-danger small">Booking Removed</span>
                                @endif
                            </td>
                            <td>
                                @if($bid->driver)
                                    {{ $bid->driver->first_name }} {{ $bid->driver->last_name }}
                                @else
                                    <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td class="fw-bold text-success">{{ $default_currency?->symbol ?? '₹' }}{{ number_format($bid->bid_amount, 2) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        'withdrawn' => 'secondary'
                                    ];
                                    $color = $statusColors[$bid->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} px-2 py-1 rounded-pill">
                                    {{ ucfirst($bid->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.bids.show', $bid->id) }}" class="btn btn-sm btn-light rounded-circle shadow-sm" title="View Details">
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No bids found.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $bids->links() }}
        </div>
    </div>
</div>
@endsection
