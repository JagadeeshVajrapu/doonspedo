@extends('layouts.admin')

@section('title', 'Bid Details')
@section('page_title', 'View Bid #' . $bid->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text text-brand me-2"></i> Bid Details</h5>
    <a href="{{ route('admin.bids.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-info-circle me-1"></i> General Information</h6>
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'withdrawn' => 'secondary'
                    ];
                    $color = $statusColors[$bid->status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} px-3 py-2 rounded-pill shadow-sm">
                    {{ ucfirst($bid->status) }}
                </span>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" width="40%">Bid ID</td>
                            <td class="fw-bold fs-5">#{{ $bid->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Booking Reference</td>
                            <td>
                                @if($bid->booking)
                                    <a href="{{ route('admin.bookings.show', $bid->booking->id) }}" class="text-decoration-none fw-bold">View Booking #{{ $bid->booking->id }} <i class="bi bi-box-arrow-up-right small ms-1"></i></a>
                                @else
                                    <span class="text-muted">Booking removed</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bid Amount</td>
                            <td class="fw-bold text-success fs-4">{{ $default_currency->symbol ?? '₹' }}{{ number_format($bid->bid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Submitted At</td>
                            <td>{{ $bid->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Driver Notes</td>
                            <td class="bg-light rounded p-3 text-muted fst-italic">{{ $bid->notes ?? 'No additional notes provided.' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="admin-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-person-badge me-1"></i> Driver Information</h6>
            </div>
            <div class="card-body p-4 text-center">
                @if($bid->driver)
                    <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                        <i class="bi bi-person-video2 text-info" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $bid->driver->first_name }} {{ $bid->driver->last_name }}</h5>
                    <p class="text-muted small mb-2"><i class="bi bi-envelope me-1"></i> {{ $bid->driver->email }}</p>
                    <p class="text-muted small mb-0"><i class="bi bi-telephone me-1"></i> {{ $bid->driver->phone_number }}</p>
                @else
                    <h5 class="text-muted">Unknown Driver</h5>
                @endif
            </div>
        </div>
        
        <div class="admin-card">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-muted mb-0"><i class="bi bi-shield-check me-1"></i> Admin Controls</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.bids.update.status', $bid->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Update Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="pending" {{ $bid->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="accepted" {{ $bid->status == 'accepted' ? 'selected' : '' }}>Approve/Accept</option>
                            <option value="rejected" {{ $bid->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                            <option value="withdrawn" {{ $bid->status == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm" type="submit">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
