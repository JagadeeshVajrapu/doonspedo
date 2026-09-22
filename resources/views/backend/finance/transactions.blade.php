@extends('layouts.admin')

@section('title', 'Transaction Logs')
@section('page_title', 'Financial Transactions')

@section('content')
<div class="admin-filter-bar">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="h6 fw-bold mb-0"><i class="bi bi-clock-history text-brand me-2"></i> All transactions</h2>
        <div class="small text-muted">Credits, debits, and future commission entries</div>
    </div>
    <form action="{{ route('admin.finance.transactions') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted" for="reference_id">Reference</label>
            <input type="text" name="reference_id" id="reference_id" class="form-control" placeholder="Search reference ID" value="{{ request('reference_id') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted" for="type">Type</label>
            <select name="type" id="type" class="form-select">
                <option value="">All Types (Credit/Debit)</option>
                <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2 flex-wrap">
            <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold"><i class="bi bi-search me-1"></i> Search</button>
            <a href="{{ route('admin.finance.transactions') }}" class="btn btn-outline-secondary rounded-pill px-3">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card mb-4">
    <div class="admin-table-wrap">
        <table class="table table-hover align-middle mb-0 admin-responsive-table">
            <thead>
                <tr>
                    <th class="py-3 px-4 text-muted small fw-bold">Date</th>
                    <th class="py-3 text-muted small fw-bold">Reference ID</th>
                    <th class="py-3 text-muted small fw-bold">User / Driver</th>
                    <th class="py-3 text-muted small fw-bold">Description</th>
                    <th class="py-3 text-muted small fw-bold">Amount</th>
                    <th class="py-3 text-muted small fw-bold">Status</th>
                    <th class="py-3 px-4 text-muted small fw-bold text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($transactions) && count($transactions) > 0)
                    @foreach($transactions as $transaction)
                    <tr>
                        <td class="px-4">
                            <div class="fw-bold small">{{ $transaction->created_at->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $transaction->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-monospace user-select-all">{{ $transaction->reference_id ?? 'SYS-'.str_pad($transaction->id, 6, "0", STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            @if($transaction->user)
                                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1"><i class="bi bi-person me-1"></i> User: {{ $transaction->user->name }}</span>
                            @elseif($transaction->driver)
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1"><i class="bi bi-car-front me-1"></i> Driver: {{ $transaction->driver->first_name }}</span>
                            @else
                                <span class="text-muted small">System / Gateway</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($transaction->description ?? 'Ride payment', 40) }}</td>
                        <td>
                            @if($transaction->type == 'credit')
                                <span class="fw-bold text-success">+{{ $default_currency?->symbol ?? '₹' }}{{ number_format($transaction->amount, 2) }}</span>
                            @else
                                <span class="fw-bold text-danger">-{{ $default_currency?->symbol ?? '₹' }}{{ number_format($transaction->amount, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->status == 'success')
                                @include('partials.ui.status-badge', ['label' => 'Success', 'variant' => 'success', 'icon' => 'bi-check-circle'])
                            @elseif($transaction->status == 'pending')
                                @include('partials.ui.status-badge', ['label' => 'Pending', 'variant' => 'warning', 'icon' => 'bi-clock'])
                            @else
                                @include('partials.ui.status-badge', ['label' => 'Failed', 'variant' => 'danger', 'icon' => 'bi-x-circle'])
                            @endif
                        </td>
                        <td class="text-end px-4">
                            <button type="button" class="btn btn-sm btn-light border rounded-circle shadow-sm" title="View Detail" aria-label="View transaction"><i class="bi bi-eye text-primary"></i></button>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="p-4">
                            @include('partials.ui.empty-state', [
                                'title' => 'No transactions yet',
                                'message' => 'Wallet credits, debits, and future commission rows will appear here.',
                                'icon' => 'bi-receipt',
                            ])
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
