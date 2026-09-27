@extends('layouts.admin')

@section('title', 'Partner Wallet')
@section('page_title', 'Partner Wallet')

@section('content')
<div class="admin-card p-4 mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="small text-muted">Partner</div>
            <div class="fw-bold">{{ $driver->name }}</div>
            <div class="small">ID #{{ $driver->id }} · {{ $driver->mobile }}</div>
        </div>
        <div class="col-md-2">
            <div class="small text-muted">Current balance</div>
            <div class="h4 fw-bold mb-0">₹{{ number_format((float) $driver->wallet_balance, 2) }}</div>
        </div>
        <div class="col-md-2">
            <div class="small text-muted">Total recharged</div>
            <div class="fw-bold">₹{{ number_format((float) $totalRecharge, 2) }}</div>
        </div>
        <div class="col-md-2">
            <div class="small text-muted">Total commission</div>
            <div class="fw-bold">₹{{ number_format((float) $totalCommission, 2) }}</div>
        </div>
        <div class="col-md-2">
            <div class="small text-muted">Completed rides</div>
            <div class="fw-bold">{{ $completedRides }}</div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header"><h2 class="admin-card-title">Transactions</h2></div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th class="px-3">Date</th><th>Description</th><th>Amount</th><th>Balance after</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td class="px-3 small">{{ $tx->created_at?->format('d M Y H:i') }}</td>
                    <td>{{ $tx->description }}</td>
                    <td class="{{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">{{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format((float) $tx->amount, 2) }}</td>
                    <td>{{ $tx->balance_after !== null ? '₹'.number_format((float) $tx->balance_after, 2) : '—' }}</td>
                    <td>{{ $tx->status }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-4 text-muted">No transactions.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $transactions->links() }}</div>
@endsection
