{{--
  Stat card
  @param string $label
  @param string|int|float $value
  @param string|null $icon  e.g. bi-people
  @param string|null $hint
  @param string|null $hintClass  e.g. text-success
--}}
<div class="ds-stat-card {{ $classExtra ?? '' }}">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="ds-stat-label mb-0">{{ $label }}</div>
        @if(!empty($icon))
            <div class="ds-stat-icon">
                <i class="bi {{ $icon }}"></i>
            </div>
        @endif
    </div>
    <div class="ds-stat-value">{{ $value }}</div>
    @if(!empty($hint))
        <p class="mb-0 mt-2 small {{ $hintClass ?? 'text-muted' }}">{{ $hint }}</p>
    @endif
</div>
