{{--
  Wallet visual state placeholders (UI only — not connected to backend logic)
  @param string $state  normal|low|blocked
  @param string $balance  display string e.g. ₹500
  @param bool $showActions  whether to show CTA buttons (visual only unless wired)
--}}
@php
    $state = $state ?? 'normal';
    $stateClass = match($state) {
        'low' => 'ds-wallet-state-low',
        'blocked' => 'ds-wallet-state-blocked',
        default => '',
    };
@endphp
<div class="ds-wallet-card {{ $stateClass }} {{ $classExtra ?? '' }}">
    <div class="ds-caption mb-2">Wallet Balance</div>
    <div class="ds-wallet-balance mb-3">{{ $balance ?? '₹0' }}</div>

    @if($state === 'low')
        <div class="ds-alert ds-alert-warning mb-3 py-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Low Wallet Balance</strong>
                <div class="small">Please recharge your wallet to continue accepting rides.</div>
            </div>
        </div>
    @elseif($state === 'blocked')
        <div class="ds-alert ds-alert-danger mb-3 py-2">
            <i class="bi bi-lock-fill"></i>
            <div>
                <strong>Wallet Balance Insufficient</strong>
                <div class="small">Please recharge your wallet before accepting rides.</div>
            </div>
        </div>
    @endif

    @if($showActions ?? true)
        <button type="button" class="btn btn-brand" disabled title="Coming soon — payment integration not enabled yet">
            {{ $state === 'blocked' ? 'Recharge Wallet' : 'Add Money' }}
        </button>
        <p class="ds-form-hint mb-0 mt-2">Visual preview only. Wallet recharge will be enabled in a later phase.</p>
    @endif
</div>
