@extends('layouts.admin')

@section('title', 'Subscription Requests')
@section('page_title', 'Manual Payment Approvals')

@section('content')
<div class="admin-card">
    <div class="card-header bg-white border-0 p-4">
        <h5 class="fw-bold mb-0">Pending Plan Requests</h5>
    </div>
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Driver</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Proof</th>
                        <th>Submitted At</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle bg-brand-soft text-brand fw-bold d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ substr($req->driver->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $req->driver->name }}</div>
                                    <div class="small text-muted">{{ $req->driver->mobile }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-dark rounded-pill px-3">{{ $req->plan->name }}</span>
                        </td>
                        <td><span class="fw-bold text-dark">₹{{ number_format($req->price, 2) }}</span></td>
                        <td>
                            @if($req->payment_proof)
                                <a href="{{ asset($req->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="bi bi-image me-1"></i> View Proof
                                </a>
                            @else
                                <span class="text-muted small">No Proof</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $req->created_at->format('d M, Y h:i A') }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <form action="{{ route('admin.subscriptions.requests.approve', $req->id) }}" method="POST" onsubmit="return confirm('Approve this subscription?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                                        <i class="bi bi-check2"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                    <i class="bi bi-x"></i> Reject
                                </button>
                            </div>

                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <form action="{{ route('admin.subscriptions.requests.reject', $req->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-0 p-4">
                                                <h5 class="modal-title fw-bold">Reject Request</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 pt-0">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">Reason for Rejection</label>
                                                    <textarea name="admin_note" class="form-control" rows="3" placeholder="e.g. Invalid screenshot, Payment not received" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-4 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger rounded-pill px-4">Reject Now</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No pending subscription requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($requests->hasPages())
    <div class="card-footer bg-white border-0 p-4">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
