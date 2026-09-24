@extends('layouts.app')

@section('title', 'Welcome to Doonspedo')
@section('body_class', 'pub-home')

@section('styles')
<link rel="preload" as="image" href="{{ asset('uploads/homepage/hero_mobility.jpg') }}" fetchpriority="high">
@endsection

@section('scripts')
<script>
    function toggleFaq(id) {
        const card = document.getElementById('faq-card-' + id);
        if (!card) return;

        const button = card.querySelector('.faq-header');
        const panel = document.getElementById('faq-panel-' + id);
        const isActive = card.classList.contains('active-faq');

        document.querySelectorAll('.faq-card').forEach(function (c) {
            c.classList.remove('active-faq');
            const btn = c.querySelector('.faq-header');
            const body = c.querySelector('.faq-body');
            if (btn) {
                btn.setAttribute('aria-expanded', 'false');
            }
            if (body) {
                body.setAttribute('aria-hidden', 'true');
            }
        });

        if (!isActive) {
            card.classList.add('active-faq');
            if (button) {
                button.setAttribute('aria-expanded', 'true');
            }
            if (panel) {
                panel.setAttribute('aria-hidden', 'false');
            }
        }
    }
</script>
@endsection

@section('content')
@php
    $brand = $sys_settings['app_name'] ?? 'Doonspedo';
    $heroTitle = $sys_settings['hero_title'] ?? null;
    $heroSubtitle = $sys_settings['hero_subtitle'] ?? 'Book city rides and parcel deliveries with verified drivers — simple booking, clear pricing, and support when you need it.';
    $heroCtaText = $sys_settings['hero_cta_text'] ?? 'Book Your Ride';
    $heroCtaLink = !empty($sys_settings['hero_cta_link']) ? $sys_settings['hero_cta_link'] : route('rider.app');
    // Custom mobility creatives
    $heroMobilityImage = asset('uploads/homepage/hero_mobility.jpg');
    $aboutImage = asset('uploads/homepage/about_mobility.jpg');
    // Safety uses a dedicated trust visual — not the fleet marketing image
    $currencySymbol = $default_currency?->symbol ?? '₹';
@endphp

    @include('layouts.header')

    <main id="main-content">

    {{-- ========== HERO (premium white — no human photo) ========== --}}
    <section class="pub-hero" aria-labelledby="hero-heading">
        <div class="pub-hero-media pub-hero-media--white" aria-hidden="true">
            <div class="pub-hero-overlay"></div>
        </div>

        <div class="container pub-hero-content">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6">
                    <p class="pub-eyebrow"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> {{ $brand }} Mobility</p>

                    <h1 id="hero-heading" class="pub-hero-title">
                        @if(!empty($heroTitle))
                            {!! $heroTitle !!}
                        @else
                            Your Ride.<br><span class="text-brand">Your Way.</span>
                        @endif
                    </h1>

                    <p class="pub-hero-copy">{{ $heroSubtitle }}</p>

                    <div class="pub-hero-actions">
                        <a href="{{ $heroCtaLink }}" class="btn btn-brand btn-lg px-4 px-md-5 active-scale">
                            {{ $heroCtaText }}
                        </a>
                        <a href="{{ route('driver.register') }}" class="btn btn-outline-brand btn-lg px-4 px-md-5 py-3">
                            Partner with Us
                        </a>
                    </div>

                    <ul class="pub-hero-trust">
                        <li><i class="bi bi-shield-check" aria-hidden="true"></i> Verified drivers</li>
                        <li><i class="bi bi-phone" aria-hidden="true"></i> Easy in-app booking</li>
                        <li><i class="bi bi-headset" aria-hidden="true"></i> Rider &amp; driver support</li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <div class="pub-hero-panel">
                        <figure class="pub-hero-visual-card pub-media-frame">
                            <img
                                class="pub-media-img"
                                src="{{ $heroMobilityImage }}"
                                alt="{{ $brand }} city rides — cab, auto, bike and van with route tracking"
                                width="1024"
                                height="768"
                                fetchpriority="high"
                                decoding="async"
                            >
                        </figure>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== TRUST STRIP ========== --}}
    <section class="pub-trust" aria-label="Why riders trust {{ $brand }}">
        <div class="container">
            <div class="row g-3 g-lg-4">
                <div class="col-6 col-lg-3">
                    <div class="pub-trust-card">
                        <div class="pub-trust-icon" aria-hidden="true"><i class="bi bi-shield-lock-fill"></i></div>
                        <h3>Safe rides</h3>
                        <p>Driver verification &amp; trip tracking</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="pub-trust-card">
                        <div class="pub-trust-icon" aria-hidden="true"><i class="bi bi-person-badge-fill"></i></div>
                        <h3>Pro drivers</h3>
                        <p>Partner captains on the platform</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="pub-trust-card">
                        <div class="pub-trust-icon" aria-hidden="true"><i class="bi bi-geo-alt-fill"></i></div>
                        <h3>Easy booking</h3>
                        <p>Pickup, destination, confirm</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="pub-trust-card">
                        <div class="pub-trust-icon" aria-hidden="true"><i class="bi bi-headset"></i></div>
                        <h3>Reliable support</h3>
                        <p>Help when you need it</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== SERVICES ========== --}}
    <section id="services" class="pub-section pub-section-light" aria-labelledby="services-heading">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 40rem;">
                <p class="pub-eyebrow justify-content-center"><i class="bi bi-grid-1x2" aria-hidden="true"></i> Services</p>
                <h2 id="services-heading" class="pub-title">Premium Taxi Services</h2>
                <p class="pub-lead mx-auto">Book taxi, auto, bike, parcel, airport transfer, and rental options — reliable for every trip.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-xl-4">
                    <article class="pub-service-card">
                        <div class="pub-service-visual">
                            <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=900&q=80" alt="City cab and taxi ride" loading="lazy" width="900" height="560" onerror="this.style.display='none'">
                        </div>
                        <div class="pub-service-icon" aria-hidden="true"><i class="bi bi-taxi-front-fill"></i></div>
                        <h3>Taxi</h3>
                        <p>Premium city taxi rides with professional drivers, clear fares, and comfortable travel across town.</p>
                        <a href="{{ route('rider.app') }}" class="pub-link">Book a taxi <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </article>
                </div>

                <div class="col-md-6 col-xl-4">
                    <article class="pub-service-card">
                        <div class="pub-service-visual">
                            <img src="{{ asset('uploads/homepage/service_auto.jpg') }}" alt="Auto rickshaw city ride" loading="lazy" width="900" height="560" onerror="this.onerror=null;this.src='https://commons.wikimedia.org/wiki/Special:FilePath/Autorickshaw.jpg?width=900';">
                        </div>
                        <div class="pub-service-icon" aria-hidden="true"><i class="bi bi-scooter"></i></div>
                        <h3>Auto</h3>
                        <p>Quick and affordable auto rides for short city hops — easy booking with transparent pricing.</p>
                        <a href="{{ route('rider.app') }}" class="pub-link">Book an auto <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </article>
                </div>

                <div class="col-md-6 col-xl-4">
                    <article class="pub-service-card">
                        <div class="pub-service-visual">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=900&q=80" alt="Bike taxi ride" loading="lazy" width="900" height="560" onerror="this.style.display='none'">
                        </div>
                        <div class="pub-service-icon" aria-hidden="true"><i class="bi bi-bicycle"></i></div>
                        <h3>Bike</h3>
                        <p>Beat traffic with bike rides for faster point-to-point travel when you need to move quickly.</p>
                        <a href="{{ route('rider.app') }}" class="pub-link">Book a bike <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </article>
                </div>

                <div class="col-md-6 col-xl-4">
                    <article class="pub-service-card">
                        <div class="pub-service-visual">
                            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=900&q=80" alt="Parcel and package delivery" loading="lazy" width="900" height="560" onerror="this.style.display='none'">
                        </div>
                        <div class="pub-service-icon" aria-hidden="true"><i class="bi bi-box-seam-fill"></i></div>
                        <h3>Parcel Delivery</h3>
                        <p>Send packages across the city using the parcel booking flow built into the rider app.</p>
                        <a href="{{ route('rider.app') }}" class="pub-link">Send a parcel <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </article>
                </div>

                <div class="col-md-6 col-xl-4">
                    <article class="pub-service-card">
                        <div class="pub-service-visual">
                            <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=900&q=80" alt="Airport and outstation transfer" loading="lazy" width="900" height="560" onerror="this.style.display='none'">
                        </div>
                        <div class="pub-service-icon" aria-hidden="true"><i class="bi bi-airplane-fill"></i></div>
                        <h3>Airport Transfer</h3>
                        <p>Reliable pickup and drop for airport trips — book ahead and travel on time with verified partners.</p>
                        <a href="{{ route('rider.app') }}" class="pub-link">Book transfer <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </article>
                </div>

                <div class="col-md-6 col-xl-4">
                    <article class="pub-service-card">
                        <div class="pub-service-visual">
                            <img src="https://images.unsplash.com/photo-1485291571150-772bcfc10da5?auto=format&fit=crop&w=900&q=80" alt="Vehicle rental for longer trips" loading="lazy" width="900" height="560" onerror="this.style.display='none'">
                        </div>
                        <div class="pub-service-icon" aria-hidden="true"><i class="bi bi-clock-history"></i></div>
                        <h3>Rental Packages</h3>
                        <p>Choose rental options when you need a vehicle for a fixed duration instead of a point-to-point trip.</p>
                        <a href="{{ route('rider.app') }}" class="pub-link">Explore rentals <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </article>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== HOW IT WORKS ========== --}}
    <section id="how-it-works" class="pub-section pub-section-light" aria-labelledby="how-heading">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 40rem;">
                <p class="pub-eyebrow justify-content-center"><i class="bi bi-signpost-2" aria-hidden="true"></i> How it works</p>
                <h2 id="how-heading" class="pub-title">Book in a few clear steps</h2>
                <p class="pub-lead mx-auto">A simple flow based on the existing {{ $brand }} rider experience.</p>
            </div>

            <div class="row g-4 pub-steps">
                <div class="col-md-6 col-lg-3">
                    <div class="pub-step">
                        <div class="pub-step-num" aria-hidden="true">1</div>
                        <h3>Enter destination</h3>
                        <p>Set your pickup and drop locations on the map or with address search.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="pub-step">
                        <div class="pub-step-num" aria-hidden="true">2</div>
                        <h3>Choose your ride</h3>
                        <p>Select from available ride types, parcel, or rental options shown in the app.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="pub-step">
                        <div class="pub-step-num" aria-hidden="true">3</div>
                        <h3>Confirm booking</h3>
                        <p>Review fare details and confirm — your request goes to available drivers.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="pub-step">
                        <div class="pub-step-num" aria-hidden="true">4</div>
                        <h3>Enjoy your trip</h3>
                        <p>Track your ride, chat when needed, and complete your journey with {{ $brand }}.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== ABOUT (CMS-driven) ========== --}}
    <section id="about" class="pub-section pub-section-light" aria-labelledby="about-heading">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-2">
                    <div class="pub-about-media pub-media-frame">
                        <img
                            class="pub-media-img"
                            src="{{ $aboutImage }}"
                            alt="{{ $brand }} mobility — bike, cab and auto with smart booking"
                            loading="lazy"
                            width="1024"
                            height="576"
                            decoding="async"
                        >
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <p class="pub-eyebrow"><i class="bi bi-info-circle" aria-hidden="true"></i> About</p>
                    <h2 id="about-heading" class="pub-title">
                        {{ $sys_settings['about_title'] ?? 'Built for everyday mobility' }}
                    </h2>
                    <div class="ds-divider mb-4"></div>
                    <div class="pub-about-copy">
                        @if(!empty($sys_settings['about_description']))
                            {!! nl2br(e($sys_settings['about_description'])) !!}
                        @else
                            <p class="mb-0">
                                {{ $brand }} connects riders and drivers through a modern booking experience —
                                from city rides to parcel delivery — with tools for tracking, support, and partner drivers.
                            </p>
                        @endif
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('policy.about') }}" class="btn btn-dark rounded-pill px-4">Learn more</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== WHY CHOOSE ========== --}}
    <section id="why-choose" class="pub-section pub-section-light pub-wcu" aria-labelledby="wcu-heading">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 42rem;">
                <p class="pub-eyebrow justify-content-center"><i class="bi bi-stars" aria-hidden="true"></i> Why {{ $brand }}</p>
                <h2 id="wcu-heading" class="pub-title">
                    @if(!empty($sys_settings['wcu_title']))
                        {!! $sys_settings['wcu_title'] !!}
                    @else
                        WHY <span class="text-brand">CHOOSE US?</span>
                    @endif
                </h2>
                <p class="pub-lead mx-auto">
                    {{ $sys_settings['wcu_subtitle'] ?? 'Practical reasons riders and partners choose Doonspedo every day.' }}
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <article class="pub-feature-card">
                        <div class="icon-wrap" aria-hidden="true">
                            <i class="bi {{ $sys_settings['wcu_feature1_icon'] ?? 'bi-clock-history' }}"></i>
                        </div>
                        <h3>{{ $sys_settings['wcu_feature1_title'] ?? '24/7 Service' }}</h3>
                        <p>{{ $sys_settings['wcu_feature1_desc'] ?? 'Always available when you need us, day or night across the city.' }}</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="pub-feature-card">
                        <div class="icon-wrap" aria-hidden="true">
                            <i class="bi {{ $sys_settings['wcu_feature2_icon'] ?? 'bi-shield-check' }}"></i>
                        </div>
                        <h3>{{ $sys_settings['wcu_feature2_title'] ?? 'Safe Rides' }}</h3>
                        <p>{{ $sys_settings['wcu_feature2_desc'] ?? 'Verified drivers and real-time tracking for your complete peace of mind.' }}</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="pub-feature-card">
                        <div class="icon-wrap" aria-hidden="true">
                            <i class="bi {{ $sys_settings['wcu_feature3_icon'] ?? 'bi-currency-rupee' }}"></i>
                        </div>
                        <h3>{{ $sys_settings['wcu_feature3_title'] ?? 'Best Price' }}</h3>
                        <p>{{ $sys_settings['wcu_feature3_desc'] ?? 'Transparent pricing with no hidden charges. Pay what you see.' }}</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== SAFETY ========== --}}
    <section id="safety" class="pub-section pub-section-light" aria-labelledby="safety-heading">
        <div class="container">
            <div class="pub-safety">
                <div>
                    <p class="pub-eyebrow"><i class="bi bi-shield-shaded" aria-hidden="true"></i> Safety &amp; trust</p>
                    <h2 id="safety-heading" class="pub-title">Travel with confidence</h2>
                    <p class="pub-lead">
                        {{ $brand }} focuses on practical safety tools already part of the product —
                        driver verification, live trip context, and support channels for riders and partners.
                    </p>
                    <ul class="pub-safety-list">
                        <li>
                            <i class="bi bi-person-check" aria-hidden="true"></i>
                            <div>
                                <strong>Driver verification</strong>
                                <span>Partners complete KYC and approval before going live on the platform.</span>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-geo" aria-hidden="true"></i>
                            <div>
                                <strong>Trip visibility</strong>
                                <span>Follow your booking status from request through completion in the app.</span>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-chat-dots" aria-hidden="true"></i>
                            <div>
                                <strong>In-app support</strong>
                                <span>Reach support from your rider or driver account when something needs attention.</span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="pub-safety-media pub-media-frame pub-safety-visual-card">
                    <div class="pub-safety-art-wrap" aria-hidden="true">
                        <svg class="pub-safety-art" viewBox="0 0 480 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Safety and trust">
                            <defs>
                                <linearGradient id="safetyBg" x1="40" y1="20" x2="440" y2="300" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#F0FDFA"/>
                                    <stop offset="1" stop-color="#ECFEFF"/>
                                </linearGradient>
                                <linearGradient id="safetyRoute" x1="80" y1="240" x2="380" y2="80" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#0F766E"/>
                                    <stop offset="1" stop-color="#14B8A6"/>
                                </linearGradient>
                            </defs>
                            <rect width="480" height="320" rx="22" fill="url(#safetyBg)"/>
                            <circle cx="390" cy="70" r="48" fill="#CCFBF1" opacity="0.75"/>
                            <circle cx="78" cy="250" r="36" fill="#99F6E4" opacity="0.4"/>
                            <path d="M240 42 C192 42, 164 68, 164 110 C164 168, 240 214, 240 214 C240 214, 316 168, 316 110 C316 68, 288 42, 240 42Z" fill="#0F766E"/>
                            <path d="M240 68 C210 68, 192 85, 192 114 C192 152, 240 184, 240 184 C240 184, 288 152, 288 114 C288 85, 270 68, 240 68Z" fill="#14B8A6"/>
                            <path d="M216 116 L234 134 L270 94" stroke="#FFFFFF" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M86 230 C140 204, 200 158, 260 140 C310 126, 340 110, 384 92" stroke="url(#safetyRoute)" stroke-width="5" stroke-linecap="round" stroke-dasharray="10 10" opacity="0.9"/>
                            <circle cx="86" cy="230" r="11" fill="#0F766E"/>
                            <circle cx="86" cy="230" r="4.5" fill="#fff"/>
                            <circle cx="384" cy="92" r="11" fill="#F59E0B"/>
                            <circle cx="384" cy="92" r="4.5" fill="#fff"/>
                        </svg>
                        <div class="pub-safety-chip">
                            <span class="pub-safety-chip-icon"><i class="bi bi-shield-check"></i></span>
                            <span>
                                <strong>Verified · Visible · Supported</strong>
                                <small>Safety tools built into every trip</small>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== DRIVER CTA + PLANS ========== --}}
    <section id="drive" class="pub-section pub-section-light" aria-labelledby="drive-heading">
        <div class="container">
            <div class="pub-driver mb-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <p class="pub-eyebrow"><i class="bi bi-car-front-fill" aria-hidden="true"></i> Partner with Us</p>
                        <h2 id="drive-heading" class="pub-title mb-3">Partner with {{ $brand }}</h2>
                        <p class="pub-lead mb-0">
                            Join as a partner captain, complete verification, add your vehicle, and start accepting rides
                            through the driver dashboard — availability, bids, earnings, and support in one place.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('driver.register') }}" class="btn btn-brand btn-lg px-4 py-3 active-scale">
                            Partner with Us
                        </a>
                        <div class="mt-3">
                            <a href="{{ route('driver.login') }}" class="btn btn-outline-brand">Partner Login</a>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($plans) && $plans->count() > 0)
                <div class="pub-plans-block">
                    <div class="text-center mb-5">
                        <h3 class="pub-plans-title">
                            <span class="pub-plans-title-main">DRIVER</span>
                            <span class="text-brand"> SUBSCRIPTION PLANS</span>
                        </h3>
                        <p class="pub-plans-sub">Choose a plan that fits your schedule and maximize your earnings.</p>
                    </div>
                    <div class="row justify-content-center g-4">
                        @foreach($plans as $plan)
                            <div class="col-md-8 col-lg-5 col-xl-4">
                                <article class="pub-plan-card">
                                    <span class="pub-plan-badge">{{ $plan->duration_days }} {{ $plan->duration_days == 1 ? 'Day' : 'Days' }}</span>
                                    <h3 class="pub-plan-name">{{ $plan->name }}</h3>
                                    <div class="pub-plan-divider" aria-hidden="true"></div>
                                    <div class="pub-plan-price-wrap">
                                        <sup class="pub-plan-currency">{{ $currencySymbol }}</sup>
                                        <span class="pub-plan-price">{{ number_format($plan->price, 2) }}</span>
                                    </div>
                                    <ul class="pub-plan-features list-unstyled mb-4">
                                        <li>
                                            <span class="pub-plan-dot" aria-hidden="true"></span>
                                            <span>Rides: <strong>{{ !empty($plan->max_rides) ? $plan->max_rides : 'UNLIMITED' }}</strong></span>
                                        </li>
                                    </ul>
                                    <a href="{{ route('driver.register') }}" class="btn pub-plan-cta w-100">
                                        JOIN AS DRIVER
                                    </a>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ========== FAQ ========== --}}
    <section id="faq" class="pub-section pub-section-light pub-faq" aria-labelledby="faq-heading">
        <div class="container">
            <div class="pub-faq-head">
                <p class="pub-eyebrow"><i class="bi bi-question-circle" aria-hidden="true"></i> FAQ</p>
                <h2 id="faq-heading" class="pub-title">Frequently Asked Questions</h2>
                <p class="pub-lead">Everything you need to know about our premium taxi service.</p>
            </div>

            <div class="pub-faq-layout">
                <div class="pub-faq-list" role="list">
                    @php
                        $fallbackFaqs = [
                            ['id' => 'f1', 'question' => 'How do I book a ride with Doonspedo?', 'answer' => 'Open the Doonspedo app or website, enter your pickup and drop locations, choose a vehicle type, review the fare, and tap Book Now. A nearby partner driver will be assigned to your trip.'],
                            ['id' => 'f2', 'question' => 'Is pricing transparent before I confirm?', 'answer' => 'Yes. You see the estimated fare before confirming your booking. Doonspedo focuses on clear pricing with no surprise charges at the end of your ride.'],
                            ['id' => 'f3', 'question' => 'Are drivers verified on the platform?', 'answer' => 'Partner drivers complete verification and approval before going online. You can also follow trip status in real time for added peace of mind.'],
                            ['id' => 'f4', 'question' => 'Can I send a parcel with Doonspedo?', 'answer' => 'Yes. Use the parcel option in the rider app to send packages across the city with the same simple booking flow you use for rides.'],
                            ['id' => 'f5', 'question' => 'How do I become a partner driver?', 'answer' => 'Tap Partner with Us, create your partner account, complete KYC and vehicle details, choose a subscription plan, and start accepting rides once approved.'],
                            ['id' => 'f6', 'question' => 'How can I contact support?', 'answer' => 'Reach us at support@doonspedo.com or call +91 96272 17655. You can also use in-app support after signing in as a rider or partner.'],
                        ];
                        $faqItems = (isset($faqs) && count($faqs) > 0) ? $faqs : collect($fallbackFaqs);
                        $faqShown = 0;
                    @endphp
                    @foreach($faqItems as $faq)
                        @php
                            $faqId = is_array($faq) ? $faq['id'] : $faq->id;
                            $q = trim((string) (is_array($faq) ? $faq['question'] : $faq->question));
                            $a = trim((string) (is_array($faq) ? $faq['answer'] : $faq->answer));
                            $looksPlaceholder = (bool) preg_match('/^(ques\d*\??|ans\d*\??)$/i', $q)
                                || (bool) preg_match('/^(ques\d*\??|ans\d*\??)$/i', $a);
                            if ($looksPlaceholder || $q === '') {
                                continue;
                            }
                            $faqShown++;
                        @endphp
                        <div class="faq-card" id="faq-card-{{ $faqId }}" role="listitem">
                            <button
                                class="faq-header"
                                type="button"
                                id="faq-btn-{{ $faqId }}"
                                aria-expanded="false"
                                aria-controls="faq-panel-{{ $faqId }}"
                                onclick="toggleFaq('{{ $faqId }}')"
                            >
                                <span class="faq-index" aria-hidden="true">{{ str_pad((string) $faqShown, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="faq-q">{{ $q }}</span>
                                <span class="faq-icon" aria-hidden="true">
                                    <i class="bi bi-plus-lg"></i>
                                </span>
                            </button>
                            <div class="faq-body" id="faq-panel-{{ $faqId }}" role="region" aria-labelledby="faq-btn-{{ $faqId }}" aria-hidden="true">
                                <div class="faq-content">
                                    {{ $a !== '' ? $a : 'Answer coming soon.' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <aside class="pub-faq-visual" aria-hidden="true">
                    <figure class="pub-faq-visual-card pub-media-frame">
                        <img
                            class="pub-media-img"
                            src="{{ $heroMobilityImage }}"
                            alt=""
                            width="1024"
                            height="576"
                            loading="lazy"
                            decoding="async"
                        >
                    </figure>
                </aside>
            </div>
        </div>
    </section>

    {{-- ========== FINAL CTA ========== --}}
    <section class="pub-section pub-final-cta" aria-labelledby="final-cta-heading">
        <div class="container">
            <p class="pub-eyebrow justify-content-center"><i class="bi bi-lightning-charge" aria-hidden="true"></i> Get started</p>
            <h2 id="final-cta-heading" class="pub-title">Ready to ride with {{ $brand }}?</h2>
            <p class="pub-lead mx-auto mb-4">Book a trip in minutes, or partner with us and start accepting rides.</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-brand btn-lg px-5 py-3 active-scale pub-cta-rider">Join as a Rider</a>
                <a href="{{ route('rider.app') }}" class="btn btn-outline-brand btn-lg px-5 py-3">Book Now</a>
                <a href="{{ route('driver.register') }}" class="btn btn-outline-brand btn-lg px-5 py-3">Partner with Us</a>
            </div>
        </div>
    </section>

    </main>

    @include('layouts.footer')
@endsection
