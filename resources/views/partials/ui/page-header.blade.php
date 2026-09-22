{{--
  Page header
  @param string $title
  @param string|null $subtitle
--}}
<div class="ds-page-header {{ $classExtra ?? '' }}">
    <div>
        <h1 class="ds-page-title mb-1">{{ $title }}</h1>
        @if(!empty($subtitle))
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        @endif
        <div class="ds-divider mt-3"></div>
    </div>
    @isset($actions)
        <div class="d-flex flex-wrap gap-2 align-items-center">
            {{ $actions }}
        </div>
    @endisset
</div>
