@extends('layouts.admin')

@section('title', 'Payment History')
@section('page_title', 'Payment History')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">Payment history</h1>
        <p class="text-muted small mb-0">Partner UPI payments stay pending until you approve them. Approval credits the wallet once.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'successful' => 'Successful', 'failed' => 'Failed', 'rejected' => 'Rejected'] as $key => $label)
            <a href="{{ route('admin.finance.recharges', ['status' => $key]) }}" class="btn btn-sm rounded-pill {{ $status === $key ? 'btn-dark' : 'btn-outline-dark' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif
@if(session('error'))
    @include('partials.ui.alert', ['variant' => 'danger', 'message' => session('error')])
@endif

<div class="admin-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th class="px-3">ID</th>
                    <th>Partner</th>
                    <th>Amount</th>
                    <th>UTR</th>
                    <th>Status</th>
                    <th>When</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recharges as $recharge)
                <tr>
                    <td class="px-3">#{{ $recharge->id }}</td>
                    <td>
                        <div class="fw-bold">{{ $recharge->driver->name ?? 'Partner' }}</div>
                        <div class="small text-muted">{{ $recharge->driver->mobile ?? '' }}</div>
                    </td>
                    <td>₹{{ number_format((float) $recharge->amount, 2) }}</td>
                    <td class="small">{{ $recharge->payment_reference ?: '—' }}</td>
                    <td class="text-capitalize">{{ $recharge->status }}</td>
                    <td class="small">{{ $recharge->created_at?->format('d M Y H:i') }}</td>
                    <td class="text-end pe-3"><a class="btn btn-sm btn-outline-dark rounded-pill" href="{{ route('admin.finance.recharges.show', $recharge->id) }}">View</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-4 text-muted">No recharge requests.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $recharges->links() }}</div>
@endsection
