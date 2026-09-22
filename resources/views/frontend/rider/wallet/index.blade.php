@extends('layouts.app')

@section('title', 'My Wallet - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
<div class="rider-shell-page">
    <header class="rider-topbar">
        <a href="{{ route('rider.app') }}" class="rider-back-btn" aria-label="Back to booking">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1>My Wallet</h1>
    </header>

    <main class="rider-content">
        <div class="rider-wallet-hero">
            <div class="position-relative" style="z-index: 1;">
                <p class="balance-label mb-0">Available Balance</p>
                <p class="balance-value">₹{{ number_format($user->wallet_balance, 2) }}</p>
                <button type="button" onclick="openAddMoneyModal()" class="btn btn-brand rounded-pill px-4 py-2 fw-bold">
                    <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Add Money
                </button>
            </div>
        </div>

        <h2 class="h6 text-muted text-uppercase ls-1 mb-3">Transaction History</h2>

        @if($transactions->isEmpty())
            @include('partials.ui.empty-state', [
                'icon' => 'bi-bank',
                'title' => 'No transactions yet',
                'message' => 'Wallet credits and payments will show up here.',
                'classExtra' => 'bg-white',
            ])
        @else
            <div class="transaction-list">
                @foreach($transactions as $transaction)
                    <div class="rider-card d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-3 p-2 me-3 {{ $transaction->type == 'credit' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                                <i class="bi {{ $transaction->type == 'credit' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }} fs-5"></i>
                            </div>
                            <div>
                                <h3 class="h6 mb-0 fw-bold">{{ $transaction->description }}</h3>
                                <p class="mb-0 text-muted x-small">{{ $transaction->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <p class="mb-0 fw-bold {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type == 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                            </p>
                            <p class="mb-0 x-small text-muted">{{ strtoupper($transaction->status) }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-center mt-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        @endif
    </main>

    @include('partials.ui.rider-bottom-nav', ['active' => 'wallet'])
</div>

<div id="add-money-modal" class="modal-backdrop-custom d-none">
    <div class="modal-content-custom bg-white p-4 shadow-lg border-top">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 fw-bold mb-0">Add Money <span class="text-brand">to Wallet</span></h2>
            <button type="button" onclick="closeAddMoneyModal()" class="btn btn-link text-dark p-0" aria-label="Close">
                <i class="bi bi-x-circle fs-4"></i>
            </button>
        </div>

        <form action="{{ route('rider.wallet.addMoney') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="text-muted small fw-bold mb-2" for="wallet-amount">AMOUNT (₹)</label>
                <div class="input-group bg-light border rounded-4 p-1">
                    <span class="input-group-text bg-transparent border-0 text-brand fs-4 fw-bold">₹</span>
                    <input type="number" id="wallet-amount" name="amount" class="form-control bg-transparent border-0 text-dark fs-4 fw-bold shadow-none" placeholder="0.00" required min="10">
                </div>
            </div>

            <div class="mb-4">
                <label class="text-muted small fw-bold mb-2">SELECT PAYMENT GATEWAY</label>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="gateway-option flex-grow-1">
                            <input type="radio" name="payment_method" value="razorpay" checked class="d-none">
                            <div class="gateway-card p-3 border rounded-4 text-center cursor-pointer">
                                <i class="bi bi-credit-card-2-front text-info fs-3 mb-1 d-block"></i>
                                <span class="small fw-bold">RazorPay</span>
                            </div>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="gateway-option flex-grow-1">
                            <input type="radio" name="payment_method" value="stripe" class="d-none">
                            <div class="gateway-card p-3 border rounded-4 text-center cursor-pointer opacity-50">
                                <i class="bi bi-stripe text-primary fs-3 mb-1 d-block"></i>
                                <span class="small fw-bold">Stripe</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-3 rounded-4 fw-bold shadow">Initialize Payment</button>
        </form>
    </div>
</div>

<style>
.gateway-option input:checked + .gateway-card {
    border-color: var(--ds-brand, #cddc29) !important;
    background: rgba(205, 220, 41, 0.08);
    opacity: 1 !important;
}
.modal-backdrop-custom {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(6px);
    z-index: 2000;
    display: flex;
    align-items: flex-end;
}
.modal-content-custom {
    width: 100%;
    border-top-left-radius: 24px;
    border-top-right-radius: 24px;
    animation: slideUp 0.3s ease-out;
}
@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
</style>

<script>
function openAddMoneyModal() { document.getElementById('add-money-modal').classList.remove('d-none'); }
function closeAddMoneyModal() { document.getElementById('add-money-modal').classList.add('d-none'); }
</script>
@endsection
