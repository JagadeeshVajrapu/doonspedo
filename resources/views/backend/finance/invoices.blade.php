@extends('layouts.admin')

@section('title', 'Invoices')
@section('page_title', 'Billing & Invoices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-brand me-2"></i> Auto-Generated Invoices</h5>
    <button class="btn btn-outline-primary rounded-pill px-4 shadow-sm" onclick="alert('Manual invoice generation coming soon')">
        <i class="bi bi-plus-lg me-1"></i> Generate Invoice
    </button>
</div>

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <!-- Filter Form -->
        <form action="{{ route('admin.finance.invoices') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="invoice_number" class="form-control rounded-pill px-3" placeholder="Search Invoice No." value="{{ request('invoice_number') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select rounded-pill px-3">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold me-2"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Invoice #</th>
                        <th class="py-3 text-muted small fw-bold border-0">Booking Ref</th>
                        <th class="py-3 text-muted small fw-bold border-0">Issue Date</th>
                        <th class="py-3 text-muted small fw-bold border-0">Due Date</th>
                        <th class="py-3 text-muted small fw-bold border-0">Total Amount</th>
                        <th class="py-3 text-muted small fw-bold border-0">Status</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($invoices) && count($invoices) > 0)
                        @foreach($invoices as $invoice)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark font-monospace user-select-all px-2 py-1 border shadow-sm">{{ $invoice->invoice_number }}</span>
                            </td>
                            <td>
                                @if($invoice->booking_id)
                                    <a href="{{ route('admin.bookings.show', $invoice->booking_id) }}" class="text-decoration-none fw-bold small">#{{ $invoice->booking_id }} <i class="bi bi-box-arrow-up-right ms-1"></i></a>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td class="small">{{ $invoice->created_at->format('M d, Y') }}</td>
                            <td class="small {{ $invoice->status == 'unpaid' && \Carbon\Carbon::parse($invoice->due_date)->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="fw-bold">{{ $default_currency?->symbol ?? '₹' }}{{ number_format($invoice->total_amount, 2) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'paid' => 'success',
                                        'unpaid' => 'warning',
                                        'cancelled' => 'danger',
                                    ];
                                    $color = $statusColors[$invoice->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} px-2 py-1 rounded-pill">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary rounded-pill shadow-sm" title="Download PDF"><i class="bi bi-download me-1"></i> PDF</button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-receipt text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No invoices generated yet.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
