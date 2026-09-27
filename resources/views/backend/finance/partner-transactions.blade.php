@extends('layouts.admin')

@section('title', 'Partner Wallet Transactions')
@section('page_title', 'Partner Wallet Transactions')

@section('content')
<form class="admin-card p-3 mb-4" method="GET">
    <div class="row g-2">
        <div class="col-md-3"><input class="form-control" name="driver" value="{{ request('driver') }}" placeholder="Partner name or mobile"></div>
        <div class="col-md-2">
            <select class="form-select" name="type">
                <option value="">All types</option>
                @foreach(['recharge' => 'Recharge', 'ride_commission' => 'Commission', 'ride_commission_failed' => 'Failed commission'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input class="form-control" name="status" value="{{ request('status') }}" placeholder="Status"></div>
        <div class="col-md-2"><input class="form-control" name="transaction_id" value="{{ request('transaction_id') }}" placeholder="Transaction ID"></div>
        <div class="col-md-1"><input class="form-control" name="ride_id" value="{{ request('ride_id') }}" placeholder="Ride"></div>
        <div class="col-md-2"><input class="form-control" name="recharge_id" value="{{ request('recharge_id') }}" placeholder="Recharge ID"></div>
        <div class="col-md-2"><input class="form-control" type="date" name="from" value="{{ request('from') }}"></div>
        <div class="col-md-2"><input class="form-control" type="date" name="to" value="{{ request('to') }}"></div>
        <div class="col-md-2"><button class="btn btn-dark rounded-pill w-100" type="submit">Filter</button></div>
    </div>
</form>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th class="px-3">Date</th>
                    <th>Partner</th>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Credit</th>
                    <th>Debit</th>
                    <th>Before</th>
                    <th>After</th>
                    <th>Reference</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td class="px-3 small">{{ $tx->created_at?->format('d M Y H:i') }}</td>
                    <td>
                        @if($tx->driver)
                            <a href="{{ route('admin.finance.partner-wallet', $tx->driver_id) }}">{{ $tx->driver->name }}</a>
                            <div class="small text-muted">{{ $tx->driver->mobile }}</div>
                        @else
                            —
                        @endif
                    </td>
                    <td>#{{ $tx->id }}</td>
                    <td class="small">{{ $tx->category ?: $tx->type }}</td>
                    <td class="text-success">{{ $tx->type === 'credit' ? '₹'.number_format((float) $tx->amount, 2) : '' }}</td>
                    <td class="text-danger">{{ $tx->type === 'debit' ? '₹'.number_format((float) $tx->amount, 2) : '' }}</td>
                    <td>{{ $tx->balance_before !== null ? '₹'.number_format((float) $tx->balance_before, 2) : '—' }}</td>
                    <td>{{ $tx->balance_after !== null ? '₹'.number_format((float) $tx->balance_after, 2) : '—' }}</td>
                    <td class="small">{{ $tx->reference_type }} {{ $tx->reference_id }}</td>
                    <td>{{ $tx->status }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="p-4 text-muted">No partner wallet transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $transactions->links() }}</div>
@endsection
