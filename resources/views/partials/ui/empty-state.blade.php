{{--
  Empty state
  @param string $title
  @param string|null $message
  @param string|null $icon  bootstrap-icons class without 'bi '
  @param string|null $actionLabel
  @param string|null $actionUrl
--}}
<div class="ds-empty {{ $classExtra ?? '' }}">
    <div class="ds-empty-icon">
        <i class="bi {{ $icon ?? 'bi-inbox' }}"></i>
    </div>
    <h5 class="ds-card-title mb-2">{{ $title }}</h5>
    @if(!empty($message))
        <p class="text-muted mb-0 mx-auto" style="max-width: 420px;">{{ $message }}</p>
    @endif
    @if(!empty($actionLabel) && !empty($actionUrl))
        <div class="mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-brand">{{ $actionLabel }}</a>
        </div>
    @endif
</div>
