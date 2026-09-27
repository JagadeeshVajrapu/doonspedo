@extends('layouts.driver')

@section('title', 'Add Money')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Add money</h1>
        <p class="text-muted small mb-0">Choose an amount. You will pay by scanning the admin QR, then submit your UTR.</p>
    </div>
</div>

@if(session('error'))
    @include('partials.ui.alert', ['variant' => 'danger', 'message' => session('error')])
@endif

<div class="drv-card p-4">
    <form action="{{ route('driver.wallet.add.submit') }}" method="POST">
        @csrf
        <label class="form-label fw-bold" for="amount">Amount (₹)</label>
        <input id="amount" name="amount" type="number" step="0.01" min="{{ $limits->min_recharge ?? 10 }}" max="{{ $limits->max_recharge ?? 50000 }}" class="form-control form-control-lg mb-3" value="{{ old('amount', '500') }}" required>
        @error('amount')<div class="text-danger small mb-3">{{ $message }}</div>@enderror
        <div class="d-flex flex-wrap gap-2 mb-4">
            @foreach([100, 200, 500, 1000, 2000] as $suggestion)
                <button type="button" class="btn btn-outline-dark rounded-pill" onclick="document.getElementById('amount').value='{{ $suggestion }}'">₹{{ number_format($suggestion) }}</button>
            @endforeach
        </div>
        <button class="btn btn-dark rounded-pill px-4 py-2" type="submit">Continue to payment</button>
    </form>
</div>
@endsection
