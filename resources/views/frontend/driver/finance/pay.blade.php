@extends('layouts.driver')

@section('title', 'Pay to add money')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Add money</h1>
        <p class="text-muted small mb-0">Scan the QR in your UPI app, then submit the payment reference. The wallet is credited only after admin verification.</p>
    </div>
</div>

@if($recharge->status !== 'pending')
    @include('partials.ui.alert', ['variant' => 'info', 'message' => 'This recharge is '.$recharge->status.'.'])
@elseif($recharge->submitted_at)
    @include('partials.ui.alert', ['variant' => 'warning', 'message' => 'Payment verification pending.'])
@endif

<div class="drv-card p-4 text-center">
    <div class="small text-muted">Amount</div>
    <div class="display-6 fw-bold mb-3">₹{{ number_format((float) $recharge->amount, 2) }}</div>
    @if($recharge->qrCode)
        <p class="fw-bold mb-2">Scan this QR code to pay</p>
        <img src="{{ $recharge->qrCode->imageUrl() }}" alt="{{ $recharge->qrCode->title }}" class="img-fluid mb-3" style="max-width: 240px;">
        @if($recharge->qrCode->is_test)
            <div class="small text-warning mb-2">TEST QR</div>
        @endif
        @if($recharge->qrCode->upi_id)
            <div class="small text-muted">UPI ID</div>
            <div class="fw-bold mb-3">{{ $recharge->qrCode->upi_id }}</div>
        @endif
    @else
        @include('partials.ui.alert', ['variant' => 'danger', 'message' => 'The payment QR is no longer available. Contact support before paying.'])
    @endif

    @if($recharge->status === 'pending')
    <form action="{{ route('driver.wallet.pay.submit', $recharge->id) }}" method="POST" class="text-start mx-auto" style="max-width: 420px;">
        @csrf
        <label class="form-label fw-bold" for="utr">Payment reference / UTR</label>
        <input id="utr" name="payment_reference" class="form-control mb-3" value="{{ old('payment_reference', $recharge->payment_reference) }}" required maxlength="80">
        @error('payment_reference')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
        <button class="btn btn-dark w-100 rounded-pill py-2" type="submit">I have paid</button>
    </form>
    @endif

    @if($recharge->status === 'rejected')
        <div class="text-danger mt-3">Recharge rejected. {{ $recharge->rejection_reason }}</div>
    @endif
</div>
@endsection
