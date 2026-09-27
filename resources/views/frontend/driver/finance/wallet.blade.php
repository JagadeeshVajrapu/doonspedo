@extends('layouts.driver')

@section('title', 'My Wallet - Doonspedo')

@section('content')
@php
    $currencySymbol = $driverCurrency?->symbol ?? '₹';
    $rate = $driverCurrency?->exchange_rate ?? 1.0;
    $balanceDisplay = $currencySymbol . number_format(($driver->wallet_balance ?? 0) * $rate, 2);
    $totalCredited = isset($transactions) ? $transactions->where('type', 'credit')->sum('amount') : 0;
    $pendingWithdrawals = isset($withdrawals) ? $withdrawals->where('status', 'pending')->sum('amount') : 0;
@endphp

<div class="drv-page-header">
    <div>
        <h1>Wallet</h1>
        <p class="text-muted mb-0 small">Manage your balance, withdrawals, and transaction history.</p>
    </div>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif
@if(session('error'))
    @include('partials.ui.alert', ['variant' => 'danger', 'message' => session('error')])
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="drv-card bg-dark text-white p-4 h-100 position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-25" aria-hidden="true">
                <i class="bi bi-wallet2 display-1"></i>
            </div>
            <div class="position-relative" style="z-index: 2;">
                <div class="small text-white-50 fw-bold text-uppercase mb-2">Available balance</div>
                <div class="display-5 fw-bold mb-3">{{ $balanceDisplay }}</div>

                @if(!empty($lowBalance))
                    <div class="alert alert-warning py-2 small">Low wallet balance. Add money to continue accepting rides.</div>
                @endif

                <div class="d-grid gap-2">
                    <a href="{{ route('driver.wallet.add') }}" class="btn btn-brand btn-lg rounded-pill fw-bold py-3">+ Add money</a>
                    <a href="{{ route('driver.wallet.history') }}" class="btn btn-outline-light rounded-pill">Transaction history</a>
                    <button type="button" class="btn btn-outline-light rounded-pill" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        Request withdrawal
                    </button>
                    <p class="extra-small text-white-50 text-center mb-0 mt-1">
                        Minimum withdrawal: {{ $currencySymbol }}{{ number_format(100 * $rate, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="row g-3 h-100">
            <div class="col-md-6">
                <div class="drv-stat h-100">
                    <div class="d-flex align-items-center mb-2 gap-2">
                        <span class="rounded-3 bg-success bg-opacity-10 text-success p-2"><i class="bi bi-arrow-down-left-circle"></i></span>
                        <span class="drv-stat-label mb-0">Total credited</span>
                    </div>
                    <div class="drv-stat-value">{{ $currencySymbol }}{{ number_format($totalCredited * $rate, 2) }}</div>
                    <div class="small text-muted mt-1">Lifetime earnings added to wallet</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="drv-stat h-100">
                    <div class="d-flex align-items-center mb-2 gap-2">
                        <span class="rounded-3 bg-primary bg-opacity-10 text-primary p-2"><i class="bi bi-clock-history"></i></span>
                        <span class="drv-stat-label mb-0">Pending payouts</span>
                    </div>
                    <div class="drv-stat-value">{{ $currencySymbol }}{{ number_format($pendingWithdrawals * $rate, 2) }}</div>
                    <div class="small text-muted mt-1">Current pending requests</div>
                </div>
            </div>
            <div class="col-12">
                <div class="drv-card p-3">
                    <div class="small fw-bold text-uppercase text-muted mb-2">Activity types</div>
                    <div class="d-flex flex-wrap gap-2">
                        @include('partials.ui.status-badge', ['label' => 'Wallet credit', 'variant' => 'success', 'icon' => 'bi-plus-circle'])
                        @include('partials.ui.status-badge', ['label' => 'Wallet debit', 'variant' => 'danger', 'icon' => 'bi-dash-circle'])
                        @include('partials.ui.status-badge', ['label' => 'Withdrawal', 'variant' => 'warning', 'icon' => 'bi-bank'])
                        @include('partials.ui.status-badge', ['label' => 'Ride commission', 'variant' => 'neutral', 'icon' => 'bi-percent'])
                    </div>
                    <p class="extra-small text-muted mb-0 mt-2">
                        @if(!empty($commission) && $commission->is_active)
                            Current commission: {{ $commission->label() }}.
                        @else
                            Prepaid ride commission is turned off. Completed rides use the existing earnings credit.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="drv-card">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                <h2 class="h6 fw-bold mb-0">Recent transactions</h2>
                <a href="{{ route('driver.wallet.history') }}" class="small fw-bold text-decoration-none">View all</a>
            </div>

            {{-- Desktop table --}}
            <div class="d-none d-md-block table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="extra-small text-uppercase fw-bold text-muted">
                            <th class="px-4 py-3">Description</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Amount</th>
                            <th class="px-4 py-3 text-end">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($transactions) && count($transactions) > 0)
                            @foreach($transactions as $tx)
                        <tr>
                            <td class="px-4">
                                <div class="small fw-bold text-dark">{{ $tx->description }}</div>
                                @if($tx->reference_id)
                                <div class="extra-small text-muted">Ref: {{ $tx->reference_id }}</div>
                                @endif
                            </td>
                            <td>
                                @include('partials.ui.status-badge', [
                                    'label' => strtoupper($tx->type),
                                    'variant' => $tx->type == 'credit' ? 'success' : 'danger',
                                ])
                            </td>
                            <td>
                                <div class="small fw-bold {{ $tx->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->type == 'credit' ? '+' : '-' }}{{ $currencySymbol }}{{ number_format($tx->amount * $rate, 2) }}
                                </div>
                            </td>
                            <td class="px-4 text-end">
                                <div class="extra-small text-muted">{{ $tx->created_at->format('d M, Y h:i A') }}</div>
                            </td>
                        </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="py-4">
                                @include('partials.ui.empty-state', [
                                    'title' => 'No transactions yet',
                                    'message' => 'Wallet credits, debits, and future commission entries will show here.',
                                    'icon' => 'bi-receipt',
                                ])
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="d-md-none p-3">
                @if(isset($transactions) && count($transactions) > 0)
                    @foreach($transactions as $tx)
                    <div class="border rounded-4 p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div class="small fw-bold">{{ $tx->description }}</div>
                            @include('partials.ui.status-badge', [
                                'label' => strtoupper($tx->type),
                                'variant' => $tx->type == 'credit' ? 'success' : 'danger',
                            ])
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold {{ $tx->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $tx->type == 'credit' ? '+' : '-' }}{{ $currencySymbol }}{{ number_format($tx->amount * $rate, 2) }}
                            </div>
                            <div class="extra-small text-muted">{{ $tx->created_at->format('d M, Y') }}</div>
                        </div>
                        @if($tx->reference_id)
                        <div class="extra-small text-muted mt-1">Ref: {{ $tx->reference_id }}</div>
                        @endif
                    </div>
                    @endforeach
                @else
                    @include('partials.ui.empty-state', [
                        'title' => 'No transactions yet',
                        'message' => 'Wallet credits, debits, and future commission entries will show here.',
                        'icon' => 'bi-receipt',
                    ])
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="drv-card">
            <div class="px-4 py-3 border-bottom">
                <h2 class="h6 fw-bold mb-0">Withdrawal history</h2>
            </div>
            <div class="p-3">
                @if(isset($withdrawals) && count($withdrawals) > 0)
                    @foreach($withdrawals as $wd)
                <div class="p-3 rounded-4 mb-2 bg-light border-start border-3 @if($wd->status == 'pending') border-warning @elseif($wd->status == 'completed') border-success @else border-danger @endif">
                    <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                        <div class="h6 fw-bold mb-0">{{ $currencySymbol }}{{ number_format($wd->amount * $rate, 2) }}</div>
                        @include('partials.ui.status-badge', [
                            'label' => strtoupper($wd->status),
                            'variant' => $wd->status == 'pending' ? 'warning' : ($wd->status == 'completed' ? 'success' : 'danger'),
                        ])
                    </div>
                    <div class="extra-small text-muted mb-1">{{ $wd->payment_method }} · {{ $wd->created_at->format('d M, Y') }}</div>
                    @if($wd->admin_note)
                    <div class="bg-white p-2 rounded-3 mt-2 extra-small border">
                        <strong>Admin:</strong> {{ $wd->admin_note }}
                    </div>
                    @endif
                </div>
                    @endforeach
                @else
                    @include('partials.ui.empty-state', [
                        'title' => 'No withdrawals',
                        'message' => 'Your withdrawal requests will appear here.',
                        'icon' => 'bi-bank',
                    ])
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Withdrawal Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-bold" id="withdrawModalLabel">Request withdrawal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form id="withdrawal-form">
                    @csrf
                    <div class="mb-4 text-center">
                        <label for="withdraw-amount" class="small text-muted fw-bold d-block mb-2">Amount to withdraw ({{ $currencySymbol }})</label>
                        <input type="number" name="amount" id="withdraw-amount" class="form-control text-center display-6 fw-bold border-0 bg-light p-3 rounded-4 shadow-none" placeholder="0.00" min="{{ 100 * $rate }}" max="{{ ($driver->wallet_balance ?? 0) * $rate }}">
                        <div class="extra-small text-muted mt-2">Available: {{ $balanceDisplay }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2" for="payment_method">Payment method</label>
                        <select name="payment_method" id="payment_method" class="form-select bg-light border-0 rounded-4 p-3 shadow-none fw-bold" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="UPI">UPI (Google Pay/PhonePe)</option>
                            <option value="PayPal">PayPal</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-2" for="payment_details">Payment details (Account No / UPI ID)</label>
                        <textarea name="payment_details" id="payment_details" class="form-control bg-light border-0 rounded-4 p-3 shadow-none" rows="2" placeholder="Enter bank details or UPI ID..." required></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-brand btn-lg py-3 rounded-pill fw-bold active-scale shadow-sm">Submit request</button>
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
.active-scale:active { transform: scale(0.95); transition: 0.1s; }
</style>

@section('scripts')
<script>
document.getElementById('withdrawal-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => data[key] = value);

    fetch('{{ route("driver.withdraw.request") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        alert('Server error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>
@endsection
@endsection
