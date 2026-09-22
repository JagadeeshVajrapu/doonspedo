{{--
  Doonspedo status badge
  @param string $label
  @param string $variant  brand|success|warning|danger|info|neutral
  @param string|null $icon  optional bootstrap-icons class e.g. bi-check-circle
--}}
@php
    $variant = $variant ?? 'neutral';
    $class = match($variant) {
        'brand' => 'ds-badge-brand',
        'success' => 'ds-badge-success',
        'warning' => 'ds-badge-warning',
        'danger' => 'ds-badge-danger',
        'info' => 'ds-badge-info',
        default => 'ds-badge-neutral',
    };
@endphp
<span class="ds-badge {{ $class }} {{ $classExtra ?? '' }}" role="status">
    @if(!empty($icon))
        <i class="bi {{ $icon }}" aria-hidden="true"></i>
    @endif
    {{ $label }}
</span>
