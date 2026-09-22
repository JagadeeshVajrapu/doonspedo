{{--
  Alert banner
  @param string $message
  @param string $variant  success|warning|danger|info
  @param string|null $icon
  @param string|null $title
--}}
@php
    $variant = $variant ?? 'info';
    $class = match($variant) {
        'success' => 'ds-alert-success',
        'warning' => 'ds-alert-warning',
        'danger' => 'ds-alert-danger',
        default => 'ds-alert-info',
    };
    $defaultIcon = match($variant) {
        'success' => 'bi-check-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'danger' => 'bi-x-circle-fill',
        default => 'bi-info-circle-fill',
    };
@endphp
<div class="ds-alert {{ $class }} {{ $classExtra ?? '' }}" role="alert" aria-live="polite">
    <i class="bi {{ $icon ?? $defaultIcon }} fs-5 flex-shrink-0" aria-hidden="true"></i>
    <div>
        @if(!empty($title))
            <div class="fw-bold mb-1">{{ $title }}</div>
        @endif
        <div>{{ $message }}</div>
    </div>
</div>
