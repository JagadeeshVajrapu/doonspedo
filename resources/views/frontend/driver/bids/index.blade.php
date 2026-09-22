@extends('layouts.driver')

@section('title', 'My Bids - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Ride requests / Bids</h1>
        <p class="text-muted mb-0 small">Track and manage your offers to passengers.</p>
    </div>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

@if(isset($bids) && count($bids) > 0)
    {{-- Desktop table --}}
    <div class="drv-card d-none d-lg-block overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-uppercase fw-bold text-muted">
                        <th class="px-4 py-3">Ride info</th>
                        <th class="py-3">Pickup & destination</th>
                        <th class="py-3">My bid</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bids as $bid)
                    <tr>
                        <td class="px-4">
                            <div class="fw-bold text-dark">#{{ $bid->booking->id }}</div>
                            <div class="small text-muted text-capitalize">{{ $bid->booking->service_type }}</div>
                        </td>
                        <td>
                            <div class="d-flex flex-column small">
                                <div class="mb-1"><i class="bi bi-circle-fill text-brand me-1" style="font-size: 0.5rem;"></i> {{ $bid->booking->pickup_location }}</div>
                                <div><i class="bi bi-geo-alt-fill text-dark me-1" style="font-size: 0.6rem;"></i> {{ $bid->booking->dropoff_location }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">₹{{ number_format($bid->bid_amount, 2) }}</div>
                            <div class="extra-small text-muted">{{ $bid->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="text-center">
                            @include('partials.ui.status-badge', [
                                'label' => strtoupper($bid->status),
                                'variant' => $bid->status == 'pending' ? 'warning' : ($bid->status == 'accepted' ? 'success' : ($bid->status == 'withdrawn' ? 'neutral' : 'danger')),
                            ])
                        </td>
                        <td class="px-4 text-end">
                            @if($bid->status == 'pending')
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold"
                                        onclick="editBid({{ $bid->id }}, {{ $bid->bid_amount }}, '{{ $bid->notes }}')">
                                    Edit
                                </button>
                                <form action="{{ route('driver.bids.withdraw', $bid->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="return confirm('Withdraw this bid?')">
                                        Withdraw
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-muted small fw-bold">No actions</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile cards --}}
    <div class="d-lg-none vstack gap-3 mb-4">
        @foreach($bids as $bid)
        <article class="drv-card p-3">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <div class="fw-bold">#{{ $bid->booking->id }} · <span class="text-capitalize">{{ $bid->booking->service_type }}</span></div>
                    <div class="extra-small text-muted">{{ $bid->created_at->diffForHumans() }}</div>
                </div>
                @include('partials.ui.status-badge', [
                    'label' => strtoupper($bid->status),
                    'variant' => $bid->status == 'pending' ? 'warning' : ($bid->status == 'accepted' ? 'success' : ($bid->status == 'withdrawn' ? 'neutral' : 'danger')),
                ])
            </div>
            <div class="mb-3 small">
                <div class="mb-2"><span class="text-muted fw-semibold">Pickup</span><br><strong>{{ $bid->booking->pickup_location }}</strong></div>
                <div><span class="text-muted fw-semibold">Destination</span><br><strong>{{ $bid->booking->dropoff_location }}</strong></div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">Your bid</span>
                <span class="fw-bold fs-5">₹{{ number_format($bid->bid_amount, 2) }}</span>
            </div>
            @if($bid->status == 'pending')
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-dark py-2 rounded-pill fw-bold"
                        onclick="editBid({{ $bid->id }}, {{ $bid->bid_amount }}, '{{ $bid->notes }}')">
                    Edit bid
                </button>
                <form action="{{ route('driver.bids.withdraw', $bid->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 py-2 rounded-pill fw-bold" onclick="return confirm('Withdraw this bid?')">
                        Withdraw
                    </button>
                </form>
            </div>
            @endif
        </article>
        @endforeach
    </div>
@else
    <div class="drv-card p-4">
        @include('partials.ui.empty-state', [
            'title' => 'No bids yet',
            'message' => 'Go to the dashboard and stay online to find rides to bid on.',
            'icon' => 'bi-lightning-charge',
            'actionLabel' => 'Open dashboard',
            'actionUrl' => route('driver.dashboard'),
        ])
    </div>
@endif

<!-- Edit Bid Modal -->
<div class="modal fade" id="editBidModal" tabindex="-1" aria-labelledby="editBidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h2 class="modal-title h6 fw-bold" id="editBidModalLabel">Modify your bid</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form id="edit-bid-form">
                    <input type="hidden" id="edit-bid-id">
                    <div class="mb-4 text-center">
                        <label for="edit-bid-amount" class="small text-muted fw-bold mb-2 d-block">New bid amount (₹)</label>
                        <input type="number" id="edit-bid-amount" class="form-control text-center fs-2 fw-bold border-0 bg-light p-3 rounded-4 shadow-none" placeholder="0.00">
                    </div>
                    <div class="mb-4">
                        <label for="edit-bid-notes" class="small fw-bold text-muted mb-2">Optional message</label>
                        <textarea id="edit-bid-notes" class="form-control bg-light border-0 rounded-4 p-3 shadow-none" rows="2" placeholder="Tell passenger why to choose you..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-brand btn-lg py-3 rounded-pill fw-bold active-scale">Update offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.bg-brand { background-color: #cddc29 !important; }
.text-brand { color: #cddc29 !important; }
.btn-brand { background-color: #cddc29; color: #000; }
.btn-brand:hover { background-color: #b9c825; color: #000; }
.extra-small { font-size: 0.65rem; }
.active-scale:active { transform: scale(0.98); }
</style>

<script>
const editModal = new bootstrap.Modal(document.getElementById('editBidModal'));

function editBid(id, amount, notes) {
    document.getElementById('edit-bid-id').value = id;
    document.getElementById('edit-bid-amount').value = amount;
    document.getElementById('edit-bid-notes').value = notes;
    editModal.show();
}

document.getElementById('edit-bid-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('edit-bid-id').value;
    const amount = document.getElementById('edit-bid-amount').value;
    const notes = document.getElementById('edit-bid-notes').value;

    fetch(`{{ url('driver/bids') }}/${id}/update`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            bid_amount: amount,
            notes: notes
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
});
</script>
@endsection
