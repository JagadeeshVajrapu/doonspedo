{{-- Public site footer --}}
@php
    $footerName = $sys_settings['app_name'] ?? 'Doonspedo';
    $footerEmail = $sys_settings['support_email'] ?? 'support@doonspedo.com';
    $footerPhone = $sys_settings['contact_phone'] ?? '+91 96272 17655';
    $footerAddress = $sys_settings['contact_address'] ?? 'Doonspedo Headquarters, City Center, India';
@endphp

<footer id="contact" class="public-footer" role="contentinfo">
    <div class="container position-relative">
        <div class="row gy-5 mb-2">
            <div class="col-lg-4 col-md-6 pe-lg-5">
                <a href="{{ url('/') }}" class="d-inline-block mb-3 text-decoration-none">
                    @if(!empty($sys_settings['app_logo']))
                        @include('partials.ui.brand-logo', ['size' => 'footer', 'alt' => $footerName])
                    @else
                        <h3 class="text-brand fw-bold mb-0">{{ $footerName }}</h3>
                    @endif
                </a>
                <p class="footer-brand-copy mb-0">
                    Experience the most premium taxi service in the city. Safe, reliable, and comfortable rides at transparent prices. Your journey is our priority.
                </p>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="footer-heading">Quick Links</h5>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li><a href="{{ route('policy.about') }}" class="footer-link">About Us</a></li>
                    <li><a href="{{ route('rider.app') }}" class="footer-link">Book a Ride</a></li>
                    <li><a href="{{ route('driver.register') }}" class="footer-link">Partner with Us</a></li>
                    <li><a href="{{ route('policy.community-guidelines') }}" class="footer-link">Community Guidelines</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="footer-heading">Legal &amp; Policies</h5>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li><a href="{{ route('policy.terms') }}" class="footer-link">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('policy.driver-terms') }}" class="footer-link">Driver Terms</a></li>
                    <li><a href="{{ route('policy.privacy') }}" class="footer-link">Privacy Policy</a></li>
                    <li><a href="{{ route('policy.refund') }}" class="footer-link">Refund &amp; Cancellation</a></li>
                    <li><a href="{{ route('policy.data-deletion') }}" class="footer-link">Data Deletion Policy</a></li>
                    <li><a href="{{ route('policy.account-deletion') }}" class="footer-link">Account Deletion Request</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="footer-heading">Contact Us</h5>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li class="footer-contact-item">
                        <span class="footer-contact-icon" aria-hidden="true"><i class="bi bi-geo-alt-fill"></i></span>
                        <span class="footer-link mt-1" style="cursor: default;">{{ $footerAddress }}</span>
                    </li>
                    <li class="footer-contact-item">
                        <span class="footer-contact-icon" aria-hidden="true"><i class="bi bi-envelope-fill"></i></span>
                        <a href="mailto:{{ $footerEmail }}" class="footer-link mt-1">{{ $footerEmail }}</a>
                    </li>
                    <li class="footer-contact-item">
                        <span class="footer-contact-icon" aria-hidden="true"><i class="bi bi-telephone-fill"></i></span>
                        <a href="tel:{{ preg_replace('/\s+/', '', $footerPhone) }}" class="footer-link mt-1">{{ $footerPhone }}</a>
                    </li>
                </ul>

                <div class="mt-4 d-flex flex-wrap gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-brand btn-sm px-3">Customer Login</a>
                    <a href="{{ route('driver.login') }}" class="btn btn-outline-brand btn-sm px-3">Partner Login</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <p class="text-secondary mb-0 small">
                &copy; {{ date('Y') }} Doonspedo. All rights reserved.
            </p>
            <p class="text-secondary mb-0 small text-md-center flex-grow-1">
                Developed by
                <a href="https://webfasttech.com/" target="_blank" rel="noopener noreferrer" class="text-brand fw-bold text-decoration-none">WebFastTechnology</a>
            </p>
        </div>
    </div>
</footer>
