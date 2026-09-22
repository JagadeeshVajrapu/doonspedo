@extends('layouts.admin')

@section('title', 'Earnings Report')
@section('page_title', 'Platform Earnings & Financials')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-coin text-brand me-2"></i> Financial Performance</h5>
    <a href="{{ route('admin.reports.earnings', ['export' => 'csv']) }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-file-earmark-excel me-1"></i> Export Data (CSV)
    </a>
</div>

<!-- Highlight Metrics -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary bg-opacity-10">
            <div class="card-body p-4 text-center">
                <i class="bi bi-wallet2 text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Total Gross Earnings</h6>
                <h3 class="fw-bold text-primary mb-0">{{ $default_currency?->symbol ?? '₹' }}{{ number_format($totalEarnings, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-success bg-opacity-10">
            <div class="card-body p-4 text-center">
                <i class="bi bi-graph-up-arrow text-success mb-3" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Net Platform Commissions</h6>
                <!-- Simulated calculation -->
                <h3 class="fw-bold text-success mb-0">{{ $default_currency?->symbol ?? '₹' }}{{ number_format($totalEarnings * 0.15, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 text-center">
                <i class="bi bi-receipt text-secondary mb-3" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold text-muted text-uppercase mb-1">Total Transactions</h6>
                <h3 class="fw-bold text-dark mb-0">{{ $transactions->total() }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Transaction Date</th>
                        <th class="py-3 text-muted small fw-bold border-0">Reference ID</th>
                        <th class="py-3 text-muted small fw-bold border-0">Source / Target</th>
                        <th class="py-3 text-muted small fw-bold border-0">Flow</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">Gross Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($transactions) && count($transactions) > 0)
                        @foreach($transactions as $txn)
                        <tr>
                            <td>
                                <div class="fw-bold small">{{ $txn->created_at->format('M d, Y') }}</div>
                                <div class="text-muted small">{{ $txn->created_at->format('h:i A') }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark font-monospace">{{ $txn->reference_id ?? 'SYS-'.str_pad($txn->id, 6, "0", STR_PAD_LEFT) }}</span></td>
                            <td>
                                @if($txn->user)
                                    <span class="text-primary small"><i class="bi bi-person me-1"></i> User: {{ $txn->user->name }}</span>
                                @elseif($txn->driver)
                                    <span class="text-info small"><i class="bi bi-car-front me-1"></i> Driver: {{ $txn->driver->name ?? 'Unknown' }}</span>
                                @else
                                    <span class="text-muted small">System</span>
                                @endif
                            </td>
                            <td>
                                @if($txn->type == 'credit')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1"><i class="bi bi-arrow-down-left"></i> IN</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1"><i class="bi bi-arrow-up-right"></i> OUT</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $txn->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $txn->type == 'credit' ? '+' : '-' }}{{ $default_currency?->symbol ?? '₹' }}{{ number_format($txn->amount, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-file-earmark-bar-graph text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No financial data available for reporting.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
