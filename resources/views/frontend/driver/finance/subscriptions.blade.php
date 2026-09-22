@extends('layouts.driver')

@section('title', 'Subscription Plans')

@section('styles')
@if(!empty($sys_settings['razorpay_key']))
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endif
@endsection

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Subscription plans</h1>
        <p class="text-muted mb-0 small">Choose a plan that fits your driving style.</p>
    </div>
    @if($driver->activeSubscription)
    <div class="text-md-end">
        @include('partials.ui.status-badge', ['label' => 'Active plan', 'variant' => 'success', 'icon' => 'bi-check-circle'])
        <div class="fw-bold text-dark mt-1">{{ strtoupper($driver->activeSubscription->plan->name) }}</div>
        <div class="extra-small text-muted">Expires: {{ optional($driver->activeSubscription->expires_at)->format('d M, Y') ?? 'N/A' }}</div>
    </div>
    @endif
</div>

<div class="row g-4 justify-content-center">
    @if(isset($plans) && count($plans) > 0)
        @foreach($plans as $plan)
    <div class="col-md-6 col-lg-4">
        <div class="drv-card h-100 {{ $driver->activeSubscription && $driver->activeSubscription->subscription_plan_id == $plan->id ? 'border border-2 border-brand' : '' }}">
            <div class="p-4 text-center border-bottom">
                <h2 class="h5 fw-bold text-dark mb-0">{{ strtoupper($plan->name) }}</h2>
                <div class="display-5 fw-bold text-brand my-3">₹{{ number_format($plan->price, 0) }}</div>
                <div class="small fw-bold text-muted text-uppercase">For {{ $plan->duration_days }} days</div>
            </div>
            <div class="p-4">
                <p class="text-muted small mb-4">{{ $plan->description }}</p>
                
                @if(isset($plan->features) && count($plan->features) > 0)
                <ul class="list-unstyled mb-4">
                    @foreach($plan->features as $feature)
                    <li class="mb-2 d-flex align-items-center small">
                        <i class="bi bi-check-circle-fill text-success me-2"></i> {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                @endif

                <div class="d-grid">
                    @if($driver->activeSubscription && $driver->activeSubscription->subscription_plan_id == $plan->id)
                        <button type="button" class="btn btn-success btn-lg rounded-pill fw-bold py-3" disabled>
                            Current plan
                        </button>
                    @else
                        <button type="button" onclick="showPaymentOptions({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->price }})" class="btn btn-dark btn-lg rounded-pill fw-bold py-3 active-scale">
                            Purchase now
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @else
    <div class="col-12">
        <div class="drv-card p-4">
            @include('partials.ui.empty-state', [
                'title' => 'No active plans',
                'message' => 'Subscription plans are not available right now.',
                'icon' => 'bi-calendar-x',
            ])
        </div>
    </div>
    @endif
</div>

<!-- Payment Selection Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-bold">Complete Your Purchase</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Selected Plan</div>
                    <h4 id="selected-plan-name" class="fw-bold text-dark mb-1"></h4>
                    <div id="selected-plan-price" class="h3 fw-bold text-brand mb-0"></div>
                </div>

                <div class="nav nav-pills custom-pills mb-4 d-flex gap-2" role="tablist">
                    <button class="nav-link active flex-fill py-3 rounded-3 fw-bold shadow-sm" id="pills-wallet-tab" data-bs-toggle="pill" data-bs-target="#pills-wallet" type="button" role="tab">
                        <i class="bi bi-wallet2 me-2"></i> Wallet
                    </button>
                    @if(isset($sys_settings['manual_payment_enabled']) && $sys_settings['manual_payment_enabled'])
                    <button class="nav-link flex-fill py-3 rounded-3 fw-bold shadow-sm" id="pills-manual-tab" data-bs-toggle="pill" data-bs-target="#pills-manual" type="button" role="tab">
                        <i class="bi bi-bank me-2"></i> Manual
                    </button>
                    @endif
                    @if(!empty($sys_settings['razorpay_key']))
                    <button class="nav-link flex-fill py-3 rounded-3 fw-bold shadow-sm" id="pills-razorpay-tab" data-bs-toggle="pill" data-bs-target="#pills-razorpay" type="button" role="tab">
                        <i class="bi bi-shield-check me-2"></i> Razorpay
                    </button>
                    @endif
                    @if(!empty($sys_settings['paypal_id']))
                    <button class="nav-link flex-fill py-3 rounded-3 fw-bold shadow-sm" id="pills-paypal-tab" data-bs-toggle="pill" data-bs-target="#pills-paypal" type="button" role="tab">
                        <i class="bi bi-paypal me-2"></i> PayPal
                    </button>
                    @endif
                </div>

                <div class="tab-content">
                    <!-- Wallet Tab -->
                    <div class="tab-pane fade show active" id="pills-wallet" role="tabpanel">
                        <div class="alert alert-light border-0 shadow-sm rounded-4 p-4 text-center">
                            <div class="small text-muted mb-2">Available Balance</div>
                            <h4 class="fw-bold mb-3">₹{{ number_format($driver->wallet_balance, 2) }}</h4>
                            <button id="wallet-pay-btn" onclick="payViaWallet()" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow">
                                <i class="bi bi-shield-lock-fill me-1"></i> PAY FROM WALLET
                            </button>
                        </div>
                    </div>

                    @if(isset($sys_settings['manual_payment_enabled']) && $sys_settings['manual_payment_enabled'])
                    <!-- Manual Tab -->
                    <div class="tab-pane fade" id="pills-manual" role="tabpanel">
                        <div class="manual-payment-info p-3 bg-light rounded-4 mb-4">
                            @if(!empty($sys_settings['manual_payment_qr']))
                            <div class="text-center mb-3">
                                <div class="small text-muted fw-bold text-uppercase mb-2">Scan & Pay</div>
                                <img src="{{ asset($sys_settings['manual_payment_qr']) }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 200px;">
                            </div>
                            @endif
                            <div class="small text-dark fw-bold mb-2">Instructions:</div>
                            <div class="small text-muted mb-3" style="white-space: pre-line;">{!! nl2br(e($sys_settings['manual_payment_instructions'] ?? 'Please transfer to our bank account and upload proof.')) !!}</div>
                        </div>
                        
                        <form id="manual-payment-form">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Upload Transaction Screenshot / Proof</label>
                                <input type="file" name="payment_proof" class="form-control rounded-3" accept="image/*" required>
                                <div class="form-text small">JPEG, PNG or JPG (Max 2MB)</div>
                            </div>
                            <button type="submit" id="manual-pay-btn" class="btn btn-brand w-100 rounded-pill py-3 fw-bold shadow text-dark">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> SUBMIT PROOF
                            </button>
                        </form>
                    </div>
                    @endif

                    @if(!empty($sys_settings['razorpay_key']))
                    <!-- Razorpay Tab -->
                    <div class="tab-pane fade" id="pills-razorpay" role="tabpanel">
                        <div class="text-center py-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/8/89/Razorpay_logo.svg" style="height: 30px;" class="mb-4">
                            <p class="text-muted small">Safe and secure payment via Razorpay.</p>
                            <button onclick="payViaRazorpay()" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow">
                                <i class="bi bi-credit-card-fill me-1"></i> PAY WITH RAZORPAY
                            </button>
                        </div>
                    </div>
                    @endif

                    @if(!empty($sys_settings['paypal_id']))
                    <!-- PayPal Tab -->
                    <div class="tab-pane fade" id="pills-paypal" role="tabpanel">
                        <div class="text-center py-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" style="height: 30px;" class="mb-4">
                            <p class="text-muted small">Global payments powered by PayPal.</p>
                            <button onclick="payViaPayPal()" class="btn btn-info w-100 rounded-pill py-3 fw-bold shadow text-white">
                                <i class="bi bi-paypal me-1"></i> PAY WITH PAYPAL
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-brand { color: #cddc29 !important; }
.bg-brand { background-color: #cddc29 !important; }
.active-scale:active { transform: scale(0.95); transition: 0.1s; }
.hover-up:hover { transform: translateY(-10px); box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important; }
.transition-all { transition: all 0.3s ease; }
.custom-pills .nav-link { color: #666; background: #f0f0f0; border: 1px solid transparent; }
.custom-pills .nav-link.active { background: #000; color: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
</style>

@endsection

@section('scripts')
<script>
let currentPlanId = null;
const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

function showPaymentOptions(planId, planName, price) {
    currentPlanId = planId;
    document.getElementById('selected-plan-name').innerText = planName;
    document.getElementById('selected-plan-price').innerText = '₹' + price.toLocaleString();
    paymentModal.show();
}

function payViaWallet() {
    const btn = document.getElementById('wallet-pay-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

    fetch('{{ route("driver.subscriptions.purchase") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ plan_id: currentPlanId })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-lock-fill me-1"></i> PAY FROM WALLET';
        }
    })
    .catch(err => {
        alert('Server error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-lock-fill me-1"></i> PAY FROM WALLET';
    });
}

function payViaRazorpay() {
    // We will initiate a server-side order first to get the order ID
    fetch('{{ route("driver.subscriptions.purchase_razorpay_init") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ plan_id: currentPlanId })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const options = {
                "key": "{{ $sys_settings['razorpay_key'] ?? '' }}",
                "amount": data.amount,
                "currency": "INR",
                "name": "{{ $sys_settings['app_name'] ?? 'Doonspedo' }}",
                "description": "Subscription Plan",
                "order_id": data.order_id,
                "handler": function (response){
                    // Finalize on server
                    fetch('{{ route("driver.subscriptions.purchase_razorpay_complete") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            plan_id: currentPlanId,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature
                        })
                    })
                    .then(res => res.json())
                    .then(result => {
                        if(result.success) {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert(result.message);
                        }
                    });
                },
                "prefill": {
                    "name": "{{ $driver->name }}",
                    "contact": "{{ $driver->mobile }}"
                },
                "theme": {
                    "color": "{{ $sys_settings['theme_color'] ?? '#ffc107' }}"
                }
            };
            const rzp1 = new Razorpay(options);
            rzp1.open();
        } else {
            alert(data.message);
        }
    });
}

function payViaPayPal() {
    alert("PayPal integration is being initialized. Redirecting to payment portal...");
    // Simplified redirect for demo or actual implementation if needed
    window.location.href = '{{ route("driver.subscriptions.purchase_paypal") }}?plan_id=' + currentPlanId;
}

document.getElementById('manual-payment-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('manual-pay-btn');
    const formData = new FormData(this);
    formData.append('plan_id', currentPlanId);

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';

    fetch('{{ route("driver.subscriptions.purchase_manual") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill me-1"></i> SUBMIT PROOF';
        }
    })
    .catch(err => {
        alert('Server error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill me-1"></i> SUBMIT PROOF';
    });
});

function purchasePlan(planId, planName, price) {
    // Keep for backward compatibility if needed, but we use showPaymentOptions now
    showPaymentOptions(planId, planName, price);
}
</script>
@endsection
