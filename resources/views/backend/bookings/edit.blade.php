@extends('layouts.admin')

@section('title', 'Edit Booking Status')
@section('page_title', 'Update Booking #' . $booking->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-brand me-2"></i> Update Status for Booking #{{ $booking->id }}</h5>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="form-label fw-bold text-muted">Current Service Type: <span class="badge bg-info text-dark ms-2">{{ ucfirst($booking->service_type) }}</span></label>
            </div>

            <div class="mb-4">
                <label for="status" class="form-label fw-bold text-muted">Booking Status</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="accepted" {{ $booking->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="ongoing" {{ $booking->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-end mt-5 border-top border-secondary border-opacity-10 pt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Update Status
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
