@extends('layouts.admin')

@section('title', 'My Bookings')
@section('page_title', 'Bookings in your Branch')

@section('content')
<div class="admin-card">
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4">Booking ID</th>
                        <th class="py-3">Pickup Location</th>
                        <th class="py-3">Fare</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td class="px-4 fw-bold text-dark">{{ $booking->displayReference() }}</td>
                        <td class="small">{{ \Illuminate\Support\Str::limit($booking->pickup_location, 50) }}</td>
                        <td class="fw-bold">₹{{ number_format($booking->fare) }}</td>
                        <td>
                            <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }} rounded-pill px-3 py-1">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="text-end px-4">
                            <a href="{{ route('branch.bookings.show', $booking->id) }}" class="btn btn-sm btn-light rounded-pill px-3 border">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No bookings found for your branch.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer bg-white border-0 p-4">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
