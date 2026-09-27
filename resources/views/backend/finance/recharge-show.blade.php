@extends('layouts.admin')

@section('title', 'Recharge #'.$recharge->id)
@section('page_title', 'Recharge #'.$recharge->id)

@section('content')
@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif
@if(session('error'))
    @include('partials.ui.alert', ['variant' => 'danger', 'message' => session('error')])
@endif

<div class="admin-card p-4 mb-4">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="small text-muted">Partner</div>
            <div class="fw-bold">{{ $recharge->driver->name ?? '—' }}</div>
            <div class="small">{{ $recharge->driver->mobile ?? '' }}</div>
        </div>
        <div class="col-md-3">
            <div class="small text-muted">Amount</div>
            <div class="fw-bold">₹{{ number_format((float) $recharge->amount, 2) }}</div>
        </div>
        <div class="col-md-3">
            <div class="small text-muted">Status</div>
            <div class="fw-bold text-capitalize">{{ $recharge->status }}</div>
        </div>
        <div class="col-md-6">
            <div class="small text-muted">Payment reference / UTR</div>
            <div>{{ $recharge->payment_reference ?: 'Not submitted yet' }}</div>
        </div>
        <div class="col-md-6">
            <div class="small text-muted">QR used</div>
            <div>{{ $recharge->qrCode->title ?? '—' }} @if($recharge->qrCode?->is_test)<span class="text-warning">TEST QR</span>@endif</div>
        </div>
        <div class="col-md-6">
            <div class="small text-muted">Verified by</div>
            <div>{{ $recharge->verifier->name ?? '—' }}</div>
            <div class="small text-muted">{{ $recharge->verified_at?->format('d M Y H:i') }}</div>
        </div>
        @if($recharge->rejection_reason)
        <div class="col-12">
            <div class="small text-muted">Rejection reason</div>
            <div>{{ $recharge->rejection_reason }}</div>
        </div>
        @endif
    </div>
</div>

@if($recharge->status === 'pending')
<div class="d-flex flex-column flex-md-row gap-3">
    <form action="{{ route('admin.finance.recharges.approve', $recharge->id) }}" method="POST">
        @csrf
        <button class="btn btn-dark rounded-pill px-4" type="submit">Approve payment</button>
    </form>
    <form action="{{ route('admin.finance.recharges.reject', $recharge->id) }}" method="POST" class="d-flex flex-column flex-sm-row gap-2">
        @csrf
        <input name="rejection_reason" class="form-control" placeholder="Rejection reason" required maxlength="255">
        <button class="btn btn-outline-danger rounded-pill" type="submit">Reject</button>
    </form>
</div>
@endif
@endsection
