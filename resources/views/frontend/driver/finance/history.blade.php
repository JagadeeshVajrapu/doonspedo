@extends('layouts.driver')

@section('title', 'Wallet history')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Transaction history</h1>
        <p class="text-muted small mb-0">Credits are added by a verified recharge. Debits include ride commission.</p>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mb-3">
    @foreach(['all' => 'All', 'credits' => 'Credits', 'debits' => 'Debits', 'recharge' => 'Recharge', 'commission' => 'Commission', 'failed' => 'Failed', 'pending' => 'Pending'] as $key => $label)
        <a href="{{ route('driver.wallet.history', ['filter' => $key]) }}" class="btn btn-sm rounded-pill {{ $filter === $key ? 'btn-dark' : 'btn-outline-dark' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="drv-card">
    @forelse($transactions as $tx)
        <div class="d-flex justify-content-between gap-3 p-3 border-bottom">
            <div>
                <div class="fw-bold">{{ $tx->description }}</div>
                <div class="small text-muted">{{ $tx->created_at?->format('d M Y H:i') }} · {{ $tx->status }}</div>
                @if($tx->balance_after !== null)
                    <div class="small text-muted">Balance after ₹{{ number_format((float) $tx->balance_after, 2) }}</div>
                @endif
            </div>
            <div class="fw-bold {{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                {{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format((float) $tx->amount, 2) }}
            </div>
        </div>
    @empty
        <div class="p-4 text-muted">No transactions for this filter.</div>
    @endforelse
</div>
<div class="mt-3">{{ $transactions->links() }}</div>
@endsection
