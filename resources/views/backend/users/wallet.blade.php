@extends('layouts.admin')

@section('title', 'Wallet Management')

@section('page_title', 'Customer Wallets')

@section('content')
{{-- Existing summary values preserved exactly (already provided by view) --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="admin-kpi border-start border-4 border-success">
            <div class="admin-kpi-label">Total platform balance</div>
            <div class="admin-kpi-value text-success">{{ $default_currency?->symbol ?? '₹' }}0.00</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-kpi">
            <div class="admin-kpi-label">Total credit today</div>
            <div class="admin-kpi-value">{{ $default_currency?->symbol ?? '₹' }}0.00</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-kpi border-start border-4 border-danger">
            <div class="admin-kpi-label">Total debits today</div>
            <div class="admin-kpi-value text-danger">-{{ $default_currency?->symbol ?? '₹' }}0.00</div>
        </div>
    </div>
</div>

{{-- Visual structure for future prepaid driver-wallet model (presentation only) --}}
<div class="admin-card p-3 mb-4">
    <div class="small fw-bold text-uppercase text-muted mb-2">Future prepaid wallet flow</div>
    <div class="d-flex flex-wrap gap-2 align-items-center small">
        <span class="badge bg-light text-dark border">Driver adds money</span>
        <i class="bi bi-arrow-right text-muted"></i>
        <span class="badge bg-light text-dark border">Payment verified</span>
        <i class="bi bi-arrow-right text-muted"></i>
        <span class="badge bg-light text-dark border">Wallet credited</span>
        <i class="bi bi-arrow-right text-muted"></i>
        <span class="badge bg-light text-dark border">Ride completed</span>
        <i class="bi bi-arrow-right text-muted"></i>
        <span class="badge bg-light text-dark border">Commission deducted</span>
    </div>
    <p class="extra-small text-muted mb-0 mt-2">Presentation only — no backend commission or recharge logic enabled in this phase.</p>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-wallet2 text-primary me-2"></i> User balances</h2>
        <div class="input-group w-auto" style="max-width: 280px;">
            <input type="text" class="form-control form-control-sm border-end-0" placeholder="Search user ID or name" aria-label="Search wallets">
            <button class="btn btn-sm btn-outline-secondary border-start-0" type="button" aria-label="Search"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="table table-hover align-middle mb-0 admin-responsive-table">
            <thead>
                <tr>
                    <th class="px-4">User</th>
                    <th>Mobile</th>
                    <th>Current balance</th>
                    <th>Last transaction</th>
                    <th class="text-end px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($users) && count($users) > 0)
                    @foreach($users as $user)
                <tr>
                    <td class="px-4 fw-bold">
                        <div>{{ $user->name }}</div>
                        <small class="text-muted fw-light">Customer {{ $user->displayReference() }}</small>
                    </td>
                    <td>{{ $user->mobile ?? 'N/A' }}</td>
                    <td>
                        <span class="badge bg-success bg-opacity-10 text-success fs-6 rounded-pill border border-success">
                            {{ $default_currency?->symbol ?? '₹' }}0.00
                        </span>
                    </td>
                    <td>
                        <div class="small fw-bold">No transactions yet</div>
                        <small class="text-muted">-</small>
                    </td>
                    <td class="text-end px-4">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#fundModal{{ $user->id }}">
                            <i class="bi bi-plus-circle"></i> Add funds
                        </button>
                    </td>
                </tr>

                <!-- Add Funds Modal (Placeholder Data) -->
                <div class="modal fade" id="fundModal{{ $user->id }}" tabindex="-1" aria-labelledby="fundModalLabel{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow">
                            <form action="#" method="POST">
                                @csrf
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold" id="fundModalLabel{{ $user->id }}">Manage funds - {{ $user->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="small text-muted mb-4">You are modifying the wallet balance for Customer {{ $user->displayReference() }}</p>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold" for="type{{ $user->id }}">Transaction type</label>
                                        <select name="type" id="type{{ $user->id }}" class="form-select">
                                            <option value="credit">Credit (Add Funds)</option>
                                            <option value="debit">Debit (Deduct Funds)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold" for="amount{{ $user->id }}">Amount ({{ $default_currency?->symbol ?? '₹' }})</label>
                                        <input type="number" step="0.01" name="amount" id="amount{{ $user->id }}" class="form-control" placeholder="0.00" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold" for="description{{ $user->id }}">Description / reason</label>
                                        <textarea name="description" id="description{{ $user->id }}" class="form-control" rows="2" placeholder="e.g. Promotional Bonus, Refund..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary fw-bold">Confirm transaction</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                    @endforeach
                @else
                <tr>
                    <td colspan="5" class="p-4">
                        @include('partials.ui.empty-state', [
                            'title' => 'No wallets to show',
                            'message' => 'Customer wallet balances will appear here when users exist.',
                            'icon' => 'bi-wallet2',
                        ])
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
