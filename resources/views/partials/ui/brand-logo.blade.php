@php
    $logoSrc = $logo ?? ($sys_settings['app_logo'] ?? null);
    $logoSize = $size ?? 'md';
    $logoAlt = $alt ?? ($sys_settings['app_name'] ?? 'Doonspedo');
@endphp
@if(!empty($logoSrc))
<span class="brand-logo-ring brand-logo-ring--{{ $logoSize }}">
    <img src="{{ asset($logoSrc) }}" alt="{{ $logoAlt }}" class="brand-logo-mark" decoding="async">
</span>
@endif
