@extends('layouts.app')

@section('title', 'Doonspedo - Book a Ride')
@section('body_class', 'bg-light text-dark overflow-hidden')
@section('needs_maps', '1')

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
@endpush

@section('content')
@php
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp

<!-- Splash Screen / Loader (Hidden after load) -->
<div id="splash" class="position-fixed top-0 start-0 w-100 h-100 bg-white d-flex flex-column align-items-center justify-content-center" style="z-index: 9999; transition: opacity 0.5s;">
    @if(!empty($sys_settings['app_logo']))
        <div class="mb-4">@include('partials.ui.brand-logo', ['size' => 'lg'])</div>
    @else
        <h1 class="text-brand fw-bold display-3 mb-3">DOONS<span class="text-dark">PEDO</span></h1>
    @endif
    <div class="spinner-border text-brand" role="status" aria-label="Loading"></div>
</div>

<div class="app-container d-flex flex-column" style="height: 100dvh; max-width: 100%; overflow: hidden;">
    <!-- Top Header -->
    <header class="rider-app-header p-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('profile.edit') }}" class="d-flex align-items-center text-decoration-none text-dark">
            <div class="bg-brand rounded-circle p-1 me-2 overflow-hidden d-flex align-items-center justify-content-center shadow" style="width: 45px; height: 45px;">
                @if(auth()->user() && auth()->user()->photo)
                    <img src="{{ asset('uploads/profiles/' . auth()->user()->photo) }}" alt="Avatar" class="w-100 h-100 object-fit-cover rounded-circle">
                @else
                    <i class="bi bi-person-fill text-dark fs-4"></i>
                @endif
            </div>
            <div>
                <p class="mb-0 small text-dark opacity-75">{{ $greeting }},</p>
                @auth
                    <h6 class="mb-0 fw-bold text-dark">{{ auth()->user()->name }}</h6>
                @else
                    <h6 class="mb-0 fw-bold">Guest User</h6>
                @endauth
            </div>
        </a>
        <a href="{{ route('rider.bookings.index') }}" class="btn btn-light border rounded-circle" aria-label="Activity and notifications">
            <i class="bi bi-bell" aria-hidden="true"></i>
        </a>
    </header>

    <!-- Map Area -->
    <main class="flex-grow-1 position-relative bg-secondary bg-opacity-10 overflow-hidden" id="main-content" aria-label="Map and booking" style="min-height: 32dvh;">
        <div id="map" class="h-100 w-100"></div>
        <!-- Center Pin (Ola Style) -->
        <div id="center-pin" class="position-absolute top-50 start-50 translate-middle d-none" style="z-index: 1000; pointer-events: none; margin-top: -20px;">
            <div class="pin-wrapper">
                <div class="pin-icon">
                    <i class="bi bi-geo-alt-fill text-brand" style="font-size: 45px; filter: drop-shadow(0 4px 4px rgba(0,0,0,0.3));"></i>
                </div>
                <div class="pin-dot"></div>
            </div>
            <div class="pin-label bg-dark text-white px-2 py-1 rounded-pill small fw-bold shadow-sm text-nowrap" style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 5px;">
                Pickup Point
            </div>
        </div>
        <!-- Mock Overlay for loading state -->
        <div id="map-placeholder" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-light" style="z-index: 5;">
            <div class="spinner-border text-brand" role="status" aria-label="Loading map"></div>
        </div>

        <!-- Map Picker Mode Panel (Rapido-Style) -->
        <div id="map-picker-panel" class="position-absolute bottom-0 start-0 w-100 p-3 d-none" style="z-index: 1000; pointer-events: none;">
            <div class="bg-white p-3 rounded-4 shadow-lg border border-light text-center" style="pointer-events: auto;">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <span class="badge bg-brand text-dark me-2 px-3 py-2 rounded-pill fw-bold" id="map-picker-badge" style="background-color: var(--primary-color) !important; color: #000 !important;">SET PICKUP</span>
                    <h6 class="mb-0 fw-bold" id="map-picker-title">Drag map to select point</h6>
                </div>
                <input type="hidden" id="map-picker-address-raw" value="">
                <p class="small text-secondary mb-3 px-2" id="map-picker-address">Detecting location...</p>
                <div class="d-flex gap-2">
                    <button onclick="confirmMapPickerSelection()" class="btn btn-brand flex-grow-1 py-3 rounded-4 fw-bold shadow">
                        <i class="bi bi-check-circle-fill me-1"></i> Confirm Location
                    </button>
                    <button onclick="stopMapPicker()" class="btn btn-light py-3 rounded-4 fw-bold px-3">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Bottom Booking Sheet -->
    <section id="main-sheet" class="rider-booking-sheet bg-white border-top border-light rounded-top-5 p-3 shadow-lg custom-scrollbar" style="margin-top: -20px; z-index: 100; position: relative; max-height: 56dvh; overflow-y: auto;">
        <div class="handle mx-auto mb-2 bg-light opacity-75 rounded-pill" style="width: 40px; height: 4px;"></div>
        
        <!-- Service Type Tabs -->
        <div id="service-tabs-container" class="d-flex mb-3 p-1 bg-light rounded-4 scroller-x" role="tablist" aria-label="Service type">
            <button onclick="setService('ride')" id="tab-ride" type="button" role="tab" aria-selected="true" class="btn btn-brand flex-grow-1 rounded-4 py-2 fw-bold small text-nowrap me-1">Ride</button>
            <button onclick="setService('parcel')" id="tab-parcel" type="button" role="tab" aria-selected="false" class="btn btn-light flex-grow-1 rounded-4 py-2 fw-bold text-dark small text-nowrap me-1">Parcel</button>
            
        </div>

        <!-- Form Container -->
        <div id="booking-forms">
            <!-- 1. Location selection form (Primary) -->
            <div id="location-sheet">
                <div class="location-form mb-3">
                    <p class="x-small text-muted text-uppercase fw-bold ls-1 mb-2" id="pickup-label">Pickup</p>
                    <div class="position-relative mb-2">
                        <div class="input-group rider-location-field border p-1 shadow-sm m-0">
                            <span class="input-group-text bg-white border-0 text-brand" aria-hidden="true">
                                <i class="bi bi-circle-fill" style="font-size: 10px;"></i>
                            </span>
                            <input type="text" id="pickup-location" class="form-control bg-white border-0 text-dark py-3" style="outline: none; box-shadow: none;" placeholder="Search pickup or use current location" value="" aria-labelledby="pickup-label" autocomplete="off" autocorrect="off" spellcheck="false">
                            <button id="detect-btn" onclick="detectLocation()" class="btn btn-link text-secondary border-0 bg-white text-nowrap small fw-bold" type="button" title="Use Current Location" aria-label="Use Current Location"><i class="bi bi-crosshair" aria-hidden="true"></i><span class="d-none d-sm-inline ms-1">Use Current Location</span></button>
                        </div>
                        <div id="location-status" class="small mt-1 px-1 d-none" role="status" aria-live="polite"></div>
                    </div>

                    <!-- Pickup to Stop Line (Initially hidden) -->
                    <div id="pickup-to-stop-line" class="ms-4 my-1 border-start border-light d-none" style="height: 20px; width: 0; border-style: dashed !important;"></div>

                    <!-- Dynamic Stop Input (Initially hidden) -->
                    <div id="stop-location-container" class="position-relative mb-2 d-none">
                        <div class="input-group rider-location-field border p-1 shadow-sm m-0">
                            <span class="input-group-text bg-white border-0 text-warning">
                                <i class="bi bi-geo-alt-fill"></i>
                            </span>
                            <input type="text" id="stop-location" class="form-control bg-white border-0 text-dark py-3" style="outline: none; box-shadow: none;" placeholder="Stop point (optional)" aria-label="Stop location" autocomplete="off" autocorrect="off" spellcheck="false">
                            <button type="button" onclick="startMapPicker('stop')" class="btn btn-link text-secondary border-0 bg-white" title="Select stop on map" aria-label="Select stop on map"><i class="bi bi-map" aria-hidden="true"></i></button>
                            <button type="button" onclick="removeStopLocation()" class="btn btn-link text-danger border-0 bg-white" title="Remove stop" aria-label="Remove stop"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                        </div>
                        <div class="ms-4 my-1 border-start border-light" style="height: 20px; width: 0; border-style: dashed !important;"></div>
                    </div>

                    <!-- Pickup to Drop Line (Visible when no stop) -->
                    <div id="pickup-to-drop-line" class="ms-4 my-1 border-start border-light" style="height: 20px; width: 0; border-style: dashed !important;"></div>

                    <p class="x-small text-muted text-uppercase fw-bold ls-1 mb-2 mt-1" id="drop-label">Destination</p>
                    <div class="position-relative mb-2">
                        <div class="input-group rider-location-field is-drop border p-1 shadow-sm m-0">
                            <span class="input-group-text bg-white border-0 text-danger" aria-hidden="true">
                                <i class="bi bi-geo-alt-fill"></i>
                            </span>
                            <input type="text" id="drop-location" class="form-control bg-white border-0 text-dark py-3" style="outline: none; box-shadow: none;" placeholder="Enter destination" aria-labelledby="drop-label" autocomplete="off" autocorrect="off" spellcheck="false">
                            <button type="button" id="add-stop-btn" onclick="addStopLocation()" class="btn btn-link text-secondary border-0 bg-white" title="Add Stop" aria-label="Add stop"><i class="bi bi-plus-lg fw-bold" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Locate on Map Helper Buttons (Rapido-Style) -->
                <div class="d-flex justify-content-between gap-2 mb-4">
                    <button type="button" onclick="startMapPicker('pickup')" class="btn btn-light flex-fill rounded-4 py-3 fw-bold text-dark border-light shadow-sm d-flex align-items-center justify-content-center" style="font-size: 13px;">
                        <i class="bi bi-geo-alt-fill text-brand me-2"></i> Locate Pickup
                    </button>
                    <button type="button" onclick="startMapPicker('drop')" class="btn btn-light flex-fill rounded-4 py-3 fw-bold text-dark border-light shadow-sm d-flex align-items-center justify-content-center" style="font-size: 13px;">
                        <i class="bi bi-map-fill text-danger me-2"></i> Locate Drop
                    </button>
                </div>

                <!-- Detailed Parcel specific details -->
                <div id="parcel-fields" class="d-none mb-4">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-light border border-light rounded-4 p-2 px-3">
                                <label class="text-secondary small fw-bold font-xs">WEIGHT (KG)</label>
                                <input type="number" id="parcel-weight" class="form-control bg-transparent border-0 text-dark p-0 fs-6" placeholder="0.0">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light border border-light rounded-4 p-2 px-3">
                                <label class="text-secondary small fw-bold font-xs">SIZE (L×W×H)</label>
                                <input type="text" id="parcel-size" class="form-control bg-transparent border-0 text-dark p-0 fs-6" placeholder="CMs">
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="bg-light border border-light rounded-4 p-2 px-3">
                                <label class="text-secondary small fw-bold font-xs">PARCEL TYPE</label>
                                <select id="parcel-type" class="form-control bg-transparent border-0 text-dark p-0 fs-6 shadow-none">
                                    <option value="general" class="bg-light">General Items</option>
                                    <option value="fragile" class="bg-light">Fragile / Glass</option>
                                    <option value="electronics" class="bg-light">Electronics</option>
                                    <option value="documents" class="bg-light">Documents</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="bg-light border border-light rounded-4 p-2 px-3">
                                <label class="text-secondary small fw-bold font-xs">PACKAGE NOTE</label>
                                <input type="text" id="parcel-note" class="form-control bg-transparent border-0 text-dark p-0 fs-6" placeholder="Any special handling instructions?">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rental Select (Hidden by default) -->
                <div id="rental-fields" class="d-none mb-4">
                    <label class="form-label text-secondary small fw-bold mb-2">SELECT PACKAGE</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($packages as $package)
                            <div onclick="selectPackage(this, {{ $package->base_price }})" class="package-capsule bg-light border border-light text-secondary rounded-pill px-3 py-2 small fw-bold cursor-pointer transition-all" data-id="{{ $package->id }}">
                                {{ $package->name }} (₹{{ number_format($package->base_price, 0) }})
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex gap-2 mb-4">
                    <button onclick="openSchedule()" class="btn btn-light py-3 rounded-4 flex-shrink-0 border-light" style="width: 60px;">
                        <i class="bi bi-calendar-event text-brand fs-5"></i>
                    </button>
                    <button onclick="startSearching()" id="search-btn" class="btn btn-brand flex-grow-1 py-3 fw-bold fs-5 rounded-4 shadow-lg border-0 transition-transform active-scale">
                        Search Rides <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- 2. Searching/Radar State (Hidden) -->
            <div id="searching-sheet" class="d-none text-center py-4">
                <div class="search-animation mb-4">
                    <div class="radar"></div>
                    <div id="service-icon" class="driver-searching-icon">
                        <i class="bi bi-car-front-fill text-brand display-4"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-2" id="search-title">Finding top drivers</h4>
                <p class="text-secondary mb-4" id="search-desc">Connecting with nearby taxis...</p>
                
                <button onclick="cancelSearch()" class="btn btn-outline-danger w-100 py-3 rounded-pill border-0 bg-danger bg-opacity-10 mt-3">
                    Cancel Request
                </button>
            </div>

            <!-- 3. Ride Options Selection (Hidden) -->
            <div id="ride-options-sheet" class="d-none">
                <div class="ride-options-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <button onclick="goBackToLocation()" class="btn btn-sm btn-light rounded-circle p-2 d-flex align-items-center justify-content-center border-0 shadow-sm" style="width: 36px; height: 36px;">
                            <i class="bi bi-arrow-left-short fs-4"></i>
                        </button>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark" id="options-title" style="font-size: 1.1rem;">Select ride category</h5>
                            <div class="d-flex align-items-center mt-0">
                                <i class="bi bi-geo-alt-fill text-brand me-1 fs-6"></i>
                                <span class="fs-6 fw-bold text-brand" id="total-distance-display">Calculating...</span>
                                <button onclick="calculateRoute()" class="btn btn-sm btn-link text-brand p-0 ms-2" title="Retry distance">
                                    <i class="bi bi-arrow-clockwise fs-6"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <span class="badge bg-brand text-dark rounded-pill py-1 px-3 mb-1">Best Price</span>
                        <div class="fw-bold text-dark d-none" id="calculation-breakdown" style="font-size: 1.1rem;">₹0 + ₹0</div>
                    </div>
                </div>


                <!-- AC / Non-AC Selection -->
                <div class="ac-selection-panel mb-2">
                    <div class="bg-light p-2 rounded-4 d-flex gap-2 border border-light">
                        <button type="button" onclick="setACPreference('ac')" id="btn-ac-pref" class="btn btn-brand flex-grow-1 py-2 rounded-3 fw-bold small transition-all">
                            <i class="bi bi-snow me-2"></i>AC Ride
                        </button>
                        <button type="button" onclick="setACPreference('non-ac')" id="btn-non-ac-pref" class="btn btn-light flex-grow-1 py-2 rounded-3 fw-bold small transition-all border-0 text-secondary">
                            <i class="bi bi-window me-2"></i>Non-AC
                        </button>
                    </div>
                </div>

                <div class="ride-options mb-2" id="ride-option-list">
                    @foreach($categories as $index => $category)
                    <div class="ride-option d-flex align-items-center justify-content-between px-3 py-2 border-bottom border-light {{ str_contains(strtolower($category->icon), 'truck') ? 'freight-cat' : 'ride-cat' }} {{ $index == 0 ? 'active' : '' }}" 
                         data-id="{{ $category->id }}" 
                         data-base="{{ $category->base_fare ?? 0 }}" 
                         data-rate="{{ $category->rate_per_km ?? 0 }}"
                         onclick="selectRide(this)">
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="ride-icon-mini me-3">
                                @php
                                    $catName = strtolower($category->name);
                                    $hasLocalImage = !empty($category->image) && file_exists(public_path('uploads/categories/' . $category->image));
                                @endphp
                                @if($hasLocalImage)
                                    <img src="{{ asset('uploads/categories/' . $category->image) }}" class="w-100 h-100 object-fit-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                @endif
                                
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="{{ $hasLocalImage ? 'display: none;' : '' }}">
                                    @if(str_contains($catName, 'scooty') || str_contains($catName, 'scooter') || str_contains($catName, 'bike') || str_contains($catName, 'two wheeler') || str_contains($catName, 'cycle'))
                                        <!-- Scooter/Scooty SVG -->
                                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-100 h-100" style="max-height: 40px;">
                                            <ellipse cx="32" cy="53" rx="22" ry="3" fill="#000000" fill-opacity="0.15"/>
                                            <path d="M48 35C48 28 44 26 38 26C34 26 30 29 27 33L21 24L17 28L23 37C20 40 18 43 18 47H48V35Z" fill="#EF4444"/>
                                            <path d="M18 47H45C47 47 48 48 48 49C48 50 47 51 45 51H22L18 47Z" fill="#475569"/>
                                            <path d="M21 22C21 20 23 18 26 18H28C31 18 33 20 33 22V32H21V22Z" fill="#F8FAFC"/>
                                            <path d="M34 26H46C49 26 50 28 50 30C50 32 48 34 45 34H34V26Z" fill="#1E293B"/>
                                            <rect x="25" y="18" width="4" height="14" fill="#cddc29"/>
                                            <rect x="23" y="15" width="8" height="3" rx="1.5" fill="#475569"/>
                                            <circle cx="27" cy="11" r="3" fill="#94A3B8"/>
                                            <line x1="27" y1="14" x2="27" y2="11" stroke="#475569" stroke-width="2"/>
                                            <circle cx="20" cy="49" r="6" fill="#0F172A"/>
                                            <circle cx="20" cy="49" r="2.5" fill="#E2E8F0"/>
                                            <circle cx="44" cy="49" r="6" fill="#0F172A"/>
                                            <circle cx="44" cy="49" r="2.5" fill="#E2E8F0"/>
                                        </svg>
                                    @elseif(str_contains($catName, 'auto') || str_contains($catName, 'rickshaw'))
                                        <!-- Auto Rickshaw SVG -->
                                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-100 h-100" style="max-height: 40px;">
                                            <ellipse cx="32" cy="52" rx="20" ry="3.5" fill="#000000" fill-opacity="0.15"/>
                                            <path d="M18 24C18 19 22 16 28 16H36C42 16 46 19 46 24V32H18V24Z" fill="#F59E0B"/>
                                            <path d="M16 32H48V42C48 44.5 46 46.5 43.5 46.5H20.5C18 46.5 16 44.5 16 42V32Z" fill="#1E293B"/>
                                            <path d="M22 19H42V29H22V19Z" fill="#38BDF8" fill-opacity="0.7"/>
                                            <path d="M24 21H40V27H24V21Z" fill="#0EA5E9" fill-opacity="0.8"/>
                                            <path d="M20 46.5H44V49.5C44 50 43.5 50.5 43 50.5H21C20.5 50.5 20 50 20 49.5V46.5Z" fill="#475569"/>
                                            <rect x="16" y="32" width="32" height="3" fill="#cddc29"/>
                                            <rect x="29" y="38" width="6" height="5" rx="1" fill="#FFF8C4"/>
                                            <circle cx="32" cy="40.5" r="1.5" fill="#FFE135"/>
                                            <circle cx="18" cy="49" r="6" fill="#0F172A"/>
                                            <circle cx="18" cy="49" r="2.5" fill="#94A3B8"/>
                                            <circle cx="46" cy="49" r="6" fill="#0F172A"/>
                                            <circle cx="46" cy="49" r="2.5" fill="#94A3B8"/>
                                            <circle cx="32" cy="51" r="5" fill="#0F172A"/>
                                            <circle cx="32" cy="51" r="2" fill="#94A3B8"/>
                                        </svg>
                                    @elseif(str_contains($catName, 'premium') || str_contains($catName, 'sedan') || str_contains($catName, 'luxury'))
                                        <!-- Sedan/Premium Car SVG -->
                                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-100 h-100" style="max-height: 40px;">
                                            <ellipse cx="32" cy="51" rx="26" ry="4" fill="#000000" fill-opacity="0.15"/>
                                            <path d="M8 38C8 34 11 31 16 29L26 21C28 19.5 34 19.5 36 21L46 29C51 31 54 34 54 38L56 44C56 46 54 48 52 48H12C10 48 8 46 8 44L8 38Z" fill="#1E293B"/>
                                            <path d="M18.5 29L26 22C27 21 35 21 36 22L43.5 29H18.5Z" fill="#0F172A"/>
                                            <path d="M20 29L26.5 23.5H35.5L42 29H20Z" fill="#334155"/>
                                            <path d="M9 39.5C9.5 38.5 11.5 38.5 13.5 39.5L14 41C13.5 42 11.5 42 9.5 41L9 39.5Z" fill="#FFF8C4"/>
                                            <circle cx="11.5" cy="40.2" r="1.5" fill="#FFE135"/>
                                            <path d="M53 39.5C52.5 38.5 50.5 38.5 48.5 39.5L48 41C48.5 42 50.5 42 52.5 41L53 39.5Z" fill="#FFF8C4"/>
                                            <circle cx="50.5" cy="40.2" r="1.5" fill="#FFE135"/>
                                            <path d="M8.2 43H53.8L54.2 44.5H7.8L8.2 43Z" fill="#cddc29"/>
                                            <circle cx="17" cy="49" r="6.5" fill="#0F172A"/>
                                            <circle cx="17" cy="49" r="3.5" fill="#94A3B8"/>
                                            <circle cx="17" cy="49" r="1.8" fill="#F8FAFC"/>
                                            <circle cx="45" cy="49" r="6.5" fill="#0F172A"/>
                                            <circle cx="45" cy="49" r="3.5" fill="#94A3B8"/>
                                            <circle cx="45" cy="49" r="1.8" fill="#F8FAFC"/>
                                        </svg>
                                    @elseif(str_contains($catName, 'truck') || str_contains($catName, 'freight') || str_contains($catName, 'loader') || str_contains($catName, 'cargo'))
                                        <!-- Truck/Freight SVG -->
                                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-100 h-100" style="max-height: 40px;">
                                            <ellipse cx="32" cy="52" rx="24" ry="3.5" fill="#000000" fill-opacity="0.15"/>
                                            <rect x="12" y="16" width="30" height="26" rx="2" fill="#cddc29"/>
                                            <line x1="18" y1="16" x2="18" y2="42" stroke="#A2B118" stroke-width="2"/>
                                            <line x1="27" y1="16" x2="27" y2="42" stroke="#A2B118" stroke-width="2"/>
                                            <line x1="36" y1="16" x2="36" y2="42" stroke="#A2B118" stroke-width="2"/>
                                            <path d="M42 24H50C52 24 54 26 54 28V42H42V24Z" fill="#475569"/>
                                            <path d="M45 27H49.5C51 27 52 28 52 29.5V33H45V27Z" fill="#38BDF8"/>
                                            <circle cx="20" cy="49" r="6.5" fill="#0F172A"/>
                                            <circle cx="20" cy="49" r="3.0" fill="#94A3B8"/>
                                            <circle cx="34" cy="49" r="6.5" fill="#0F172A"/>
                                            <circle cx="34" cy="49" r="3.0" fill="#94A3B8"/>
                                            <circle cx="48" cy="49" r="6.5" fill="#0F172A"/>
                                            <circle cx="48" cy="49" r="3.0" fill="#94A3B8"/>
                                        </svg>
                                    @else
                                        <!-- Hatchback/Economy Car SVG -->
                                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-100 h-100" style="max-height: 40px;">
                                            <ellipse cx="32" cy="50" rx="24" ry="4" fill="#000000" fill-opacity="0.15"/>
                                            <path d="M12 36C12 32 16 30 20 28L28 20C30 18 36 18 38 20L44 28C48 30 52 32 52 36L54 44C54 46 52 48 50 48H14C12 48 10 46 10 44L12 36Z" fill="#8A99AD"/>
                                            <path d="M22 28.5L28.5 21.5C29.5 20.5 32.5 20.5 33.5 21.5L40 28.5H22Z" fill="#2E3A59"/>
                                            <path d="M24 28.5L29 23H33L38 28.5H24Z" fill="#4B6584"/>
                                            <path d="M30 23.5H32L34.5 28.5H33.5L31.5 24.5H29.5L30 23.5Z" fill="#FFF" fill-opacity="0.5"/>
                                            <circle cx="15" cy="40" r="3" fill="#FFF8C4"/>
                                            <circle cx="15" cy="40" r="1.5" fill="#FFE135"/>
                                            <circle cx="49" cy="40" r="3" fill="#FFF8C4"/>
                                            <circle cx="49" cy="40" r="1.5" fill="#FFE135"/>
                                            <rect x="22" y="42" width="20" height="3" rx="1.5" fill="#1E272C"/>
                                            <rect x="20" y="46" width="24" height="2.5" rx="1.2" fill="#0F172A"/>
                                            <path d="M11 43.5C14 43.5 16 43.8 20 44H44C48 43.8 50 43.5 53 43.5C53.8 44 54 44.5 54 45H10C10 44.5 10.2 44 11 43.5Z" fill="#cddc29"/>
                                            <circle cx="18" cy="48" r="6" fill="#1E293B"/>
                                            <circle cx="18" cy="48" r="3" fill="#64748B"/>
                                            <circle cx="18" cy="48" r="1.5" fill="#F8FAFC"/>
                                            <circle cx="44" cy="48" r="6" fill="#1E293B"/>
                                            <circle cx="44" cy="48" r="3" fill="#64748B"/>
                                            <circle cx="44" cy="48" r="1.5" fill="#F8FAFC"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">
                                    {{ $category->name }} 
                                    <span class="ms-1 text-secondary" style="font-size: 0.75rem;"><i class="bi bi-person-fill"></i>{{ $category->capacity_seats }}</span>
                                </h6>
                                @php
                                    $waitTime = rand(2, 8);
                                @endphp
                                <p class="mb-0 text-secondary" style="font-size: 0.75rem;">
                                    <span class="mins-away-display">{{ $waitTime }}</span> mins away • Drop <span class="drop-time-display" data-wait="{{ $waitTime }}">--:--</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0 fw-bold text-dark fare-display-item" id="fare-cat-{{ $category->id }}" style="font-size: 1.1rem;">@if((float) $category->base_fare > 0)₹{{ number_format($category->base_fare, 0) }}@else—@endif</h6>
                            @if($index == 0)
                                <span class="text-success fw-bold" style="font-size: 0.65rem;">BEST PRICE</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>


                <!-- Price Breakdown Section (New) -->
                <div id="price-breakdown" class="mb-3 p-3 bg-light rounded-4" style="font-size: 0.85rem;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Base Fare</span>
                        <span class="text-dark fw-bold" id="breakdown-base">Rs. 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Distance (<span id="breakdown-km">0.0</span> km)</span>
                        <span class="text-dark fw-bold" id="breakdown-dist-fare">Rs. 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between border-top border-light mt-2 pt-2">
                        <span class="text-dark fw-bold">Subtotal</span>
                        <span class="text-dark fw-bold" id="breakdown-total">Rs. 0.00</span>
                    </div>
                </div>

                <div class="payment-method-section mb-3 border-top pt-3 d-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3 cursor-pointer" onclick="setPaymentMethod('cash')">
                            <i class="bi bi-cash-stack text-success fs-5"></i>
                            <span class="fw-bold text-dark" id="current-payment-display">Cash</span>
                            <i class="bi bi-chevron-right text-secondary small"></i>
                        </div>
                        <div class="vr mx-2" style="height: 20px;"></div>
                        <div class="d-flex align-items-center gap-2 cursor-pointer" onclick="applyCoupon()">
                            <i class="bi bi-percent text-brand fs-5"></i>
                            <span class="fw-bold text-dark">Offers</span>
                            <i class="bi bi-chevron-right text-secondary small"></i>
                        </div>
                    </div>
                    <input type="hidden" id="selected-payment-method" value="cash">
                </div>

                <div class="mb-3 p-3 bg-light rounded-4" id="customer-offer-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-secondary">Estimated Fare</span>
                        <span class="fw-bold text-dark" id="estimated-fare">—</span>
                    </div>
                    <div class="fw-bold text-dark mb-2">Make an Offer</div>
                    <div class="d-flex flex-wrap gap-2 mb-3" role="group" aria-label="Add to the estimated fare">
                        @foreach ([5, 10, 15, 20] as $extra)
                        <button type="button" class="btn btn-outline-dark btn-sm rounded-pill fw-bold" style="min-width:4.25rem;min-height:2.5rem;" data-offer-extra="{{ $extra }}" aria-pressed="false" onclick="selectCustomerOffer({{ $extra }}, this)">+₹{{ $extra }}</button>
                        @endforeach
                    </div>
                    <label class="form-label small text-secondary mb-1" for="custom-offer">Optional custom offer</label>
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">₹</span>
                        <input id="custom-offer" type="number" min="0" step="1" inputmode="decimal" class="form-control" placeholder="Add a custom amount" oninput="setCustomCustomerOffer()">
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top pt-2">
                        <span class="fw-bold text-dark">Final Offer</span>
                        <span class="fw-bold text-brand" id="final-offer">—</span>
                    </div>
                </div>
                </div>

                <div class="booking-action-section">
                    <button type="button" onclick="confirmBooking()" class="btn btn-brand w-100 py-3 fw-bold fs-5 rounded-4 shadow-lg active-scale border-0 d-flex justify-content-between align-items-center px-4">
                        <div class="text-start">
                            <p class="mb-0 x-small opacity-75 fw-normal">Total Price</p>
                            <span>Book <span id="selected-ride-name">Ride</span></span>
                        </div>
                        <span id="total-fare" class="fs-4">—</span>
                    </button>
                </div>
            </div>

            <!-- 4. Bid List & Selection (New) -->
            <div id="bids-sheet" class="d-none">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Ride request</h5>
                    <span class="badge bg-brand text-dark rounded-pill py-1 px-3" id="bid-count">0 Bids</span>
                </div>
                
                <p class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-4">
                    <span class="text-secondary small mb-0">Your offer</span>
                    <strong id="quoted-fare">—</strong>
                </p>
                <div id="bid-list" class="mb-4 custom-scrollbar" style="max-height: 350px; overflow-y: auto;">
                    <!-- Bids will be injected here -->
                    <div class="text-center py-5 opacity-50">
                        <div class="spinner-border text-brand spinner-border-sm mb-3" role="status"></div>
                        <p>Waiting for a driver to accept your offer...</p>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button onclick="cancelBookingAction()" class="btn btn-outline-danger w-100 py-3 rounded-pill border-0 bg-danger bg-opacity-10 mt-3">
                        Cancel Request
                    </button>
                </div>
            </div>

            <!-- 5. Real-time Tracking & Logistics (Modified) -->
            <div id="tracking-sheet" class="d-none">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-brand" id="tracking-title">Driver is on the way!</h5>
                        <div id="parcel-badge" class="badge bg-warning text-dark d-none mb-2">Picked Up • In Transit</div>
                        <p class="small text-secondary mb-0" id="tracking-desc">Delivery partner arriving in 5 mins</p>
                        <p class="small text-dark mb-0 mt-1" id="tracking-route"></p>
                    </div>
                    <div class="rounded-circle overflow-hidden border border-brand shadow" style="width: 55px; height: 55px;">
                        <img id="driver-photo" src="https://i.pravatar.cc/100?u=driver1" class="w-100 h-100 object-fit-cover shadow">
                    </div>
                </div>
                
                <div class="driver-info p-3 bg-light rounded-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-light">
                        <div>
                            <h6 id="driver-name" class="mb-1 text-dark">Ramesh Kumar</h6>
                            <p id="driver-info" class="mb-0 small text-secondary"><i class="bi bi-star-fill text-brand"></i> 4.9 Driver • White Maruti Swift</p>
                        </div>
                        <div class="text-end">
                            <h6 id="vehicle-reg" class="mb-1 text-brand">UP 16 AT 4567</h6>
                            <p class="mb-0 small text-secondary">Fare: <span id="final-fare" class="text-dark fw-bold">—</span></p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <a id="driver-phone" href="tel:+910000000000" class="btn btn-white shadow-sm flex-grow-1 rounded-pill py-2 small fw-bold border-light">
                            <i class="bi bi-telephone-fill me-2"></i> Call
                        </a>
                        <button type="button" class="btn btn-white shadow-sm flex-grow-1 rounded-pill py-2 small fw-bold border-light" onclick="openDriverChat()">
                            <i class="bi bi-chat-dots-fill me-2"></i> Message
                        </button>
                    </div>
                </div>

                <div id="ride-otp-box" class="d-none text-center rounded-4 p-3 mb-3" style="background:#14171c;color:#fff;">
                    <p class="small mb-1" style="opacity:.75;">Ride OTP</p>
                    <p id="ride-otp-code" class="h2 fw-bold mb-1" style="letter-spacing:0.28em;">------</p>
                    <p id="ride-otp-note" class="small mb-0" style="opacity:.75;">Share this with your partner only after they reach you.</p>
                </div>

                <div class="ride-controls d-flex gap-2">
                    <button onclick="openCancelModal()" class="btn btn-outline-danger flex-grow-1 py-3 rounded-4 border-0 bg-danger bg-opacity-10 fw-bold shadow-sm">Cancel Ride</button>
                    <a id="chat-driver-link" href="#" class="btn btn-light py-3 rounded-4 border-light shadow-sm" style="width: 60px;" aria-label="Chat with driver" onclick="return openDriverChat()"><i class="bi bi-chat-dots-fill text-brand" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Rating Modal -->
    <div id="rating-modal" class="modal-backdrop-custom d-none">
        <div class="modal-content-custom bg-white p-4 shadow-lg border-top border-light text-center">
            <div class="mx-auto mb-3 bg-light rounded-pill" style="width: 40px; height: 4px;"></div>
            
            <i class="bi bi-star-fill text-brand display-4 mb-3 d-block"></i>
            <h4 class="fw-bold mb-2">How was your trip?</h4>
            <p class="text-secondary small mb-4">Tell us about your experience with your driver.</p>

            <div class="d-flex justify-content-center gap-3 mb-4 rating-stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star-fill fs-1 text-light cursor-pointer star-icon shadow-sm" data-value="{{ $i }}" onclick="setRating({{ $i }})"></i>
                @endfor
            </div>
            <input type="hidden" id="submit-rating-value" value="0">

            <div class="mb-4 text-start">
                <label class="text-secondary small fw-bold mb-2">WRITTEN FEEDBACK (OPTIONAL)</label>
                <textarea id="submit-rating-comment" class="form-control bg-light border-light text-dark py-3 rounded-4" placeholder="Any special comments?"></textarea>
            </div>

            <button id="post-review-btn" onclick="submitReview()" class="btn btn-brand w-100 py-3 rounded-4 fw-bold shadow">Submit Feedback</button>
        </div>
    </div>

    <!-- Schedule Modal (Floating) -->
    <div id="schedule-modal" class="modal-backdrop-custom d-none" role="dialog" aria-modal="true" aria-labelledby="schedule-modal-title" aria-hidden="true">
        <div class="modal-content-custom bg-white p-4 shadow-lg border-top border-light">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0" id="schedule-modal-title">Schedule <span class="text-brand">for later</span></h5>
                <button type="button" onclick="closeSchedule()" class="btn btn-link text-dark p-0" aria-label="Close schedule dialog"><i class="bi bi-x-circle fs-4" aria-hidden="true"></i></button>
            </div>
            <div class="mb-4">
                <label class="form-label text-secondary small fw-bold" for="schedule-time">DATE & TIME</label>
                <input type="datetime-local" id="schedule-time" class="form-control bg-light border-light text-dark py-3 rounded-4">
            </div>
            <button type="button" onclick="setSchedule()" class="btn btn-brand w-100 py-3 rounded-4 fw-bold shadow">Apply Schedule Time</button>
        </div>
    </div>

    <div id="cancel-modal" class="modal-backdrop-custom d-none" role="dialog" aria-modal="true" aria-labelledby="cancel-modal-title" aria-hidden="true">
        <div class="modal-content-custom bg-white p-4 shadow-lg border-top border-light">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" id="cancel-modal-title">Cancel this ride</h5>
                <button type="button" onclick="closeCancelModal()" class="btn btn-link text-dark p-0" aria-label="Close cancel dialog"><i class="bi bi-x-circle fs-4" aria-hidden="true"></i></button>
            </div>
            <p class="text-secondary small mb-3">Choose a reason. The ride is cancelled only after you select one.</p>
            <div class="d-grid gap-2 mb-3" id="cancel-reason-list">
                @foreach(\App\Http\Controllers\Frontend\RiderBookingController::CANCEL_REASONS as $reason)
                    <label class="cancel-reason-option">
                        <input type="radio" name="cancel_reason" value="{{ $reason }}">
                        <span>{{ $reason }}</span>
                    </label>
                @endforeach
            </div>
            <p id="cancel-reason-error" class="text-danger small d-none mb-3">Please select a reason to cancel this ride.</p>
            <div class="d-grid gap-2">
                <button type="button" id="cancel-confirm-btn" onclick="submitCancellation()" class="btn btn-danger w-100 py-3 rounded-4 fw-bold">Cancel ride</button>
                <button type="button" onclick="closeCancelModal()" class="btn btn-light w-100 py-3 rounded-4 fw-bold border">Keep this ride</button>
            </div>
        </div>
    </div>

    <!-- Location Permission Modal (Floating) -->
    <div id="location-permission-modal" class="modal-backdrop-custom d-none" role="dialog" aria-modal="true" aria-labelledby="permission-status-title" aria-hidden="true">
        <div class="modal-content-custom bg-white p-4 shadow-lg border-top border-light">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" id="location-modal-title">Location <span class="text-brand">Settings</span></h5>
                <button type="button" onclick="closeLocationModal()" class="btn btn-link text-dark p-0" aria-label="Close location settings"><i class="bi bi-x-circle fs-4" aria-hidden="true"></i></button>
            </div>
            
            <div class="text-center mb-4 py-2">
                <div id="permission-status-icon" class="mb-3"></div>
                <h6 id="permission-status-title" class="fw-bold mb-1">Checking Location Status...</h6>
                <p id="permission-status-desc" class="small text-secondary mb-0">We need your permission to find your exact house.</p>
            </div>

            <!-- Detailed Steps (Visible if Blocked) -->
            <div id="permission-guide-container" class="bg-light p-3 rounded-4 mb-4 d-none">
                <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-info-circle-fill text-brand me-1"></i> How to Allow Location</h6>
                <ol class="small text-secondary ps-3 mb-0" style="line-height: 1.6;">
                    <li>Tap the lock icon near the URL, then open Site settings</li>
                    <li>Set Permissions → <b>Location</b> to <b>Allow</b></li>
                    <li>Refresh this page and try again</li>
                </ol>
            </div>

            <div class="d-flex gap-2">
                <button id="modal-allow-btn" onclick="checkAndRequestLocation()" class="btn btn-brand flex-grow-1 py-3 rounded-4 fw-bold shadow">
                    <i class="bi bi-crosshair me-1"></i> Allow & Detect Location
                </button>
                <button onclick="clearLocationCache()" class="btn btn-outline-secondary py-3 rounded-4 fw-bold px-3" title="Clear cached selection">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Nav -->
    <footer class="rider-bottom-nav flex-column align-items-stretch" style="z-index: 1000;">
        <div class="d-flex justify-content-around w-100">
            <a href="{{ route('rider.app') }}" class="rider-nav-item {{ Request::routeIs('rider.app') ? 'is-active' : '' }}">
                <i class="bi bi-house-door{{ Request::routeIs('rider.app') ? '-fill' : '' }}"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('rider.bookings.index') }}" class="rider-nav-item {{ Request::routeIs('rider.bookings.*') ? 'is-active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Activity</span>
            </a>
            <a href="{{ route('rider.wallet.index') }}" class="rider-nav-item {{ Request::routeIs('rider.wallet.*') ? 'is-active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Wallet</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="rider-nav-item {{ Request::routeIs('profile.edit') ? 'is-active' : '' }}">
                <i class="bi bi-person{{ Request::routeIs('profile.edit') ? '-fill' : '' }}"></i>
                <span>Account</span>
            </a>
            <a href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to logout?')) { document.getElementById('logout-form').submit(); }" class="rider-nav-item text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
        <div class="d-flex justify-content-center flex-wrap gap-3 pt-2 border-top w-100 mt-1" style="font-size: 11px;">
            <a href="/policy/privacy" class="text-secondary text-decoration-none fw-semibold">Privacy</a>
            <a href="/policy/data-deletion" class="text-secondary text-decoration-none fw-semibold">Data Deletion</a>
            <a href="/policy/refund" class="text-secondary text-decoration-none fw-semibold">Refunds</a>
        </div>
        @include('partials.ui.developer-credit')
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </footer>
</div>

<style>
:root {
    --primary-color: #cddc29;
}
.bg-dark-custom {
    background-color: #ffffff;
}
.rounded-top-5 {
    border-top-left-radius: 35px;
    border-top-right-radius: 35px;
}
.scroller-x {
    overflow-x: auto;
    scrollbar-width: none;
}
.scroller-x::-webkit-scrollbar { display: none; }

.ride-option {
    background: transparent;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.2s ease;
    cursor: pointer;
}
.ride-option:hover {
    background: #fdfdfd;
}
.ride-option.active {
    background: rgba(205, 220, 41, 0.12);
    border-left: 4px solid var(--primary-color) !important;
}
#main-sheet.sheet-picking {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
#main-sheet.sheet-picking #booking-forms {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
    overflow: hidden;
}
#ride-options-sheet:not(.d-none) {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
    overflow: hidden;
}
#ride-options-sheet .ride-options-body {
    overflow-y: auto;
    flex: 1 1 auto;
    min-height: 0;
}
#ride-options-sheet .booking-action-section {
    flex: 0 0 auto;
    background: #fff;
    padding-top: 0.5rem;
}
.ride-icon-mini {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.package-capsule.active {
    background: var(--primary-color) !important;
    color: #000 !important;
    border-color: var(--primary-color) !important;
}
.modal-backdrop-custom {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(8px);
    z-index: 2000;
    display: flex;
    align-items: flex-end;
}
@keyframes pulse-success {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(40, 167, 69, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}
@keyframes pulse-danger {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(220, 53, 69, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}
@keyframes pulse-warning {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(255, 193, 7, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}
.modal-content-custom {
    width: 100%;
    border-top-left-radius: 30px;
    border-top-right-radius: 30px;
    animation: slideUp 0.3s ease-out;
}
#cancel-modal .modal-content-custom {
    max-height: 88vh;
    overflow-y: auto;
}
.cancel-reason-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
    padding: 0.85rem 1rem;
    border: 1px solid #e6e8ee;
    border-radius: 14px;
    background: #f7f8fa;
    cursor: pointer;
    font-weight: 600;
    color: #1c2208;
}
.cancel-reason-option:has(input:checked) {
    border-color: #1c2208;
    background: #fff;
    box-shadow: inset 4px 0 0 #cddc29;
}
.cancel-reason-option input {
    width: 1.05rem;
    height: 1.05rem;
    accent-color: #1c2208;
    flex: 0 0 auto;
}
@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
.search-animation {
    position: relative;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.radar {
    position: absolute;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 2px solid var(--primary-color);
    animation: radar-pulse 2s infinite;
    background: rgba(205, 220, 41, 0.1);
}
@keyframes radar-pulse {
    0% { transform: scale(0.5); opacity: 1; border-width: 4px; }
    100% { transform: scale(1.5); opacity: 0; border-width: 1px; }
}
.driver-searching-icon {
    position: relative;
    z-index: 2;
    animation: bounce 2s infinite ease-in-out;
}
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.pin-wrapper { 
    position: relative; 
    display: flex;
    flex-direction: column;
    align-items: center;
}
.pin-icon { 
    transform-origin: bottom center; 
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
}
.map-moving .pin-icon { 
    transform: translateY(-15px) scale(1.1); 
}
.pin-dot { 
    width: 6px; 
    height: 3px; 
    background: rgba(0,0,0,0.4); 
    border-radius: 50%; 
    transition: all 0.3s ease;
    filter: blur(1px);
}
.map-moving .pin-dot {
    transform: scale(0.5);
    opacity: 0.3;
}
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
.input-group input {
    z-index: 5 !important;
    position: relative;
    pointer-events: auto !important;
}
.pac-container {
    z-index: 5000 !important;
}
</style>

<script>
(function () {
    const originalError = console.error;
    console.error = function () {
        const message = Array.prototype.join.call(arguments, ' ');
        if (message.indexOf('RefererNotAllowedMapError') !== -1) {
            window.__mapsReferrerBlocked = true;
        }
        return originalError.apply(console, arguments);
    };
})();
let currentService = 'ride';
let baseFare = 0;
let estimatedRideFare = 0;
let customerOfferExtra = 0;
let discount = 0;
let scheduledTime = null;
let selectedPaymentMethod = 'cash';
const categories = @json($categories);
const pricingSettings = {
    rain_surge_enabled: "{{ $sys_settings['rain_surge_enabled'] ?? '0' }}" === '1',
    rain_surge_multiplier: parseFloat("{{ $sys_settings['rain_surge_multiplier'] ?? '1.5' }}"),
    night_premium_enabled: "{{ $sys_settings['night_premium_enabled'] ?? '0' }}" === '1',
    night_premium_amount: parseFloat("{{ $sys_settings['night_premium_amount'] ?? '5.0' }}"),
    ac_rate_per_km: parseFloat("{{ $sys_settings['ac_rate_per_km'] ?? '15.0' }}"),
    non_ac_rate_per_km: parseFloat("{{ $sys_settings['non_ac_rate_per_km'] ?? '10.0' }}"),
    dehradun_bike_rate_per_km: parseFloat("{{ $sys_settings['dehradun_bike_rate_per_km'] ?? '8' }}"),
    dehradun_auto_rate_per_km: parseFloat("{{ $sys_settings['dehradun_auto_rate_per_km'] ?? '12' }}"),
    dehradun_car_rate_per_km: parseFloat("{{ $sys_settings['dehradun_car_rate_per_km'] ?? '20' }}"),
    dehradun_max_local_km: parseFloat("{{ $sys_settings['dehradun_max_local_km'] ?? '40' }}")
};

function dehradunLocalRate(catName) {
    if (currentService !== 'ride') return null;
    const pickup = (document.getElementById('pickup-location')?.value || '');
    const point = (typeof toPlainLatLng === 'function' && pickupLatLng) ? toPlainLatLng(pickupLatLng) : null;
    const inBox = point && point.lat >= 30.24 && point.lat <= 30.42 && point.lng >= 77.93 && point.lng <= 78.18;
    if (point && !inBox) return null;
    if (!inBox && !/\bdehradun\b/i.test(pickup)) return null;
    const name = (catName || '').toLowerCase();
    if (name.includes('bike') || name.includes('cycle') || name.includes('moto')) return pricingSettings.dehradun_bike_rate_per_km;
    if (name.includes('auto')) return pricingSettings.dehradun_auto_rate_per_km;
    if (name.includes('cab') || name.includes('car') || name.includes('suv') || name.includes('sedan')) return pricingSettings.dehradun_car_rate_per_km;
    return null;
}
let acPreference = 'ac';

// Map Variables
let map, pickupMarker, dropMarker, stopMarker;
let directionsService, directionsRenderer;
let pickupAutocomplete, dropAutocomplete;
let pickupLatLng = null;
let dropLatLng = null;
let stopLatLng = null;
let isGoogleMaps = false; // Flag to track which provider is active
let lastCalculatedDistance = 0; // Global tracker for distance
let lastCalculatedDuration = 0; // seconds
let mapInitialized = false;
let isDetectingLocation = false;
let suppressMapIdle = false;
let routeRequestId = 0;
const mapProvider = "{{ $sys_settings['map_provider'] ?? 'google' }}";
const defaultMapLoc = { lat: 30.3165, lng: 78.0322 }; // Dehradun city center (map only)

function toPlainLatLng(latlng) {
    if (!latlng) return null;
    try {
        if (Array.isArray(latlng)) {
            const lat = parseFloat(latlng[0]);
            const lng = parseFloat(latlng[1]);
            if (isNaN(lat) || isNaN(lng)) return null;
            return { lat, lng };
        }
        const lat = typeof latlng.lat === 'function' ? latlng.lat() : latlng.lat;
        const lng = typeof latlng.lng === 'function' ? latlng.lng() : (latlng.lng ?? latlng.lon);
        if (lat === undefined || lng === undefined || isNaN(lat) || isNaN(lng)) return null;
        return { lat: parseFloat(lat), lng: parseFloat(lng) };
    } catch (e) {
        return null;
    }
}

function showLocationMessage(message, type) {
    const el = document.getElementById('location-status');
    if (!el) return;
    if (window.__mapsReferrerBlocked && message && type !== 'error') {
        message = message + ' Google Maps blocked this address. Allow this site on the API key.';
        type = 'warning';
    }
    el.classList.remove('d-none', 'text-danger', 'text-success', 'text-muted', 'text-warning');
    if (type === 'error') el.classList.add('text-danger');
    else if (type === 'success') el.classList.add('text-success');
    else if (type === 'warning') el.classList.add('text-warning');
    else el.classList.add('text-muted');
    el.textContent = message || '';
    if (!message) el.classList.add('d-none');
}

function clearLocationMessage() {
    showLocationMessage('', 'muted');
}

// Make initMap global for Google Maps callback
window.initMap = function() {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        setTimeout(window.initMap, 100);
        return;
    }

    if (mapInitialized) return;
    mapInitialized = true;

    const defaultLoc = defaultMapLoc;
    
    if (mapProvider === 'google' && typeof google !== 'undefined' && google.maps) {
        try {
            isGoogleMaps = true;
            map = new google.maps.Map(mapElement, {
                center: defaultLoc,
                zoom: 14,
                disableDefaultUI: true,
                gestureHandling: 'greedy',
                styles: [
                    { "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }] },
                    { "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] },
                    { "elementType": "labels.text.fill", "stylers": [{ "color": "#616161" }] },
                    { "elementType": "labels.text.stroke", "stylers": [{ "color": "#f5f5f5" }] },
                    { "featureType": "administrative.land_parcel", "elementType": "labels.text.fill", "stylers": [{ "color": "#bdbdbd" }] },
                    { "featureType": "poi", "elementType": "geometry", "stylers": [{ "color": "#eeeeee" }] },
                    { "featureType": "road", "elementType": "geometry", "stylers": [{ "color": "#ffffff" }] },
                    { "featureType": "road.highway", "elementType": "geometry", "stylers": [{ "color": "#dadada" }] },
                    { "featureType": "water", "elementType": "geometry", "stylers": [{ "color": "#c9c9c9" }] }
                ]
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: true,
                polylineOptions: { strokeColor: '#cddc29', strokeWeight: 5 }
            });

            document.getElementById('map-placeholder').style.display = 'none';
            document.getElementById('center-pin').classList.remove('d-none');
            // Do NOT pretreat city-center as the rider's pickup; wait for GPS or Places.
            pickupLatLng = null;
            initAutocomplete();
            detectLocation();

            // Google Maps Drag Event to Select Exact Location
            map.addListener('dragstart', () => {
                document.getElementById('center-pin').classList.add('map-moving');
            });
            
            map.addListener('idle', () => {
                document.getElementById('center-pin').classList.remove('map-moving');
                if (suppressMapIdle || isDetectingLocation) return;
                
                const center = map.getCenter();
                if (!center) return;
                const currentLatLng = { lat: center.lat(), lng: center.lng() };
                
                if (typeof activeMapPickerType !== 'undefined' && activeMapPickerType) {
                    reverseGeocodeGoogle(currentLatLng, 'map-picker-address');
                    return;
                }
                
                const locationSheet = document.getElementById('location-sheet');
                if (!locationSheet || locationSheet.classList.contains('d-none')) return;
                
                pickupLatLng = currentLatLng;
                
                const pickupInput = document.getElementById('pickup-location');
                if (pickupInput) pickupInput.value = 'Finding place...';
                
                reverseGeocodeGoogle(currentLatLng, 'pickup-location', () => {
                    updateMarker('pickup', pickupLatLng);
                    if (dropLatLng) calculateRoute();
                });
            });

        } catch (e) {
            console.error("Google Maps init failed, switching to Leaflet", e);
            mapInitialized = false;
            showLocationMessage('Google Maps is temporarily unavailable. Using offline maps.', 'warning');
            initLeaflet(defaultLoc);
        }
    } else {
        initLeaflet(defaultLoc);
    }
};

function reverseGeocodeGoogle(latlng, inputId, onSuccess) {
    const input = document.getElementById(inputId);
    if (!input || typeof google === 'undefined') return;

    const requestId = String(Date.now());
    input.dataset.lastRequest = requestId;
    setElementText(input, 'Finding place...');

    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: latlng }, (results, status) => {
        if (input.dataset.lastRequest !== requestId) return;
        if (status === 'OK' && results[0]) {
            setElementText(input, results[0].formatted_address);
            if (typeof onSuccess === 'function') onSuccess(results[0]);
        } else {
            setElementText(input, `${latlng.lat.toFixed(5)}, ${latlng.lng.toFixed(5)}`);
            showLocationMessage('Unable to resolve address. Coordinates were saved — you can edit the address manually.', 'warning');
            if (typeof onSuccess === 'function') onSuccess(null);
        }
    });
}


function initLeaflet(loc) {
    isGoogleMaps = false;
    mapInitialized = true;
    if (map && typeof map.remove === 'function') {
        try { map.remove(); } catch (e) {}
        map = null;
    }
    pickupLatLng = null; // Wait for GPS / Places; do not invent pickup coords
    const mapElement = document.getElementById('map');
    
    // Clear the element
    mapElement.innerHTML = '';
    
    map = L.map(mapElement, {
        zoomControl: false,
        attributionControl: false
    }).setView([loc.lat, loc.lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    document.getElementById('map-placeholder').style.display = 'none';
    document.getElementById('center-pin').classList.remove('d-none');

    initAutocomplete();
    
    // Map Move Logic for Exact Location
    let isMoving = false;
    map.on('movestart', () => {
        isMoving = true;
        document.getElementById('center-pin').classList.add('map-moving');
    });

    map.on('moveend', () => {
        isMoving = false;
        document.getElementById('center-pin').classList.remove('map-moving');
        if (suppressMapIdle || isDetectingLocation) return;
        
        const center = map.getCenter();

        // If we are in Map Picker mode, update picker address and do nothing else
        if (typeof activeMapPickerType !== 'undefined' && activeMapPickerType) {
            reverseGeocode(center.lat, center.lng, 'map-picker-address');
            return;
        }
        
        // Only update pickup if we are in the initial selection state
        if (document.getElementById('location-sheet').classList.contains('d-none')) return;
        
        pickupLatLng = [center.lat, center.lng];
        
        // Update Pickup Address automatically
        reverseGeocode(center.lat, center.lng, 'pickup-location');
        
        // Recalculate route if destination is already set
        if (dropLatLng) calculateRoute();
    });

    detectLocation();
}

function holdMapIdle(ms) {
    suppressMapIdle = true;
    clearTimeout(window.__mapIdleHold);
    window.__mapIdleHold = setTimeout(() => { suppressMapIdle = false; }, ms || 1600);
}

function keepTypedLocation(input) {
    if (!input) return;
    input.dataset.lastRequest = 'kept-' + Date.now();
}

function setElementText(el, text) {
    if (!el) return;
    if (el.tagName === 'INPUT') {
        el.value = text;
    } else {
        el.innerText = text;
    }
}

function reverseGeocode(lat, lng, inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    setElementText(input, "Finding place...");
    
    const requestId = Date.now();
    input.dataset.lastRequest = requestId;

    // Use Nominatim first
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&email=support@doonspedo.com&lat=${lat}&lon=${lng}&zoom=19&addressdetails=1`, {
        headers: { 'Accept-Language': 'en' }
    })
    .then(res => {
        if (!res.ok) throw new Error("Nominatim status not OK");
        return res.json();
    })
    .then(data => {
        if (input.dataset.lastRequest != requestId) return;

        if (data && data.display_name) {
            let address = data.display_name;
            if (address.endsWith(', India')) {
                address = address.substring(0, address.length - 7);
            }
            setElementText(input, address);
        } else if (data && data.address) {
            setElementText(input, "Location found");
        } else {
            fallbackToPhoton(lat, lng, input, requestId);
        }
    })
    .catch(err => {
        console.warn("Nominatim reverse geocode failed, trying Photon fallback...", err);
        if (input.dataset.lastRequest == requestId) {
            fallbackToPhoton(lat, lng, input, requestId);
        }
    });
}

function fallbackToPhoton(lat, lng, input, requestId) {
    fetch(`https://photon.komoot.io/reverse?lon=${lng}&lat=${lat}`)
    .then(res => res.json())
    .then(data => {
        if (input.dataset.lastRequest != requestId) return;
        
        if (data && data.features && data.features.length > 0) {
            const prop = data.features[0].properties;
            let display = [];
            
            if (prop.name) display.push(prop.name);
            if (prop.housenumber) display.push(prop.housenumber);
            if (prop.street && prop.street !== prop.name) display.push(prop.street);
            if (prop.locality) display.push(prop.locality);
            if (prop.district) display.push(prop.district);
            if (prop.city || prop.town) display.push(prop.city || prop.town);
            if (prop.state) display.push(prop.state);
            
            if (display.length > 0) {
                setElementText(input, display.join(', ').trim());
            } else {
                setCoordinateFallback(lat, lng, input, requestId);
            }
        } else {
            setCoordinateFallback(lat, lng, input, requestId);
        }
    })
    .catch(err => {
        console.warn("Photon fallback failed too:", err);
        if (input.dataset.lastRequest == requestId) {
            setCoordinateFallback(lat, lng, input, requestId);
        }
    });
}

function setCoordinateFallback(lat, lng, input, requestId) {
    if (input.dataset.lastRequest != requestId) return;
    
    // If it is close to the default Dehradun location, display clean place name instead of numbers
    if (Math.abs(lat - 30.3165) < 0.01 && Math.abs(lng - 78.0322) < 0.01) {
        setElementText(input, "Dehradun, Uttarakhand");
    } else {
        setElementText(input, `${lat.toFixed(5)}, ${lng.toFixed(5)}`);
    }
}

let placeSearchLock = false;

function placeInputId(kind) {
    if (kind === 'pickup') return 'pickup-location';
    if (kind === 'stop') return 'stop-location';
    return 'drop-location';
}

function hidePlaceSuggestions() {
    document.querySelectorAll('.place-suggest').forEach((el) => el.classList.add('d-none'));
}

function positionPlaceBox(input, box) {
    const rect = input.getBoundingClientRect();
    const margin = 8;
    const width = Math.min(Math.max(rect.width, 220), window.innerWidth - margin * 2);
    const left = Math.max(margin, Math.min(rect.left, window.innerWidth - width - margin));
    const spaceBelow = window.innerHeight - rect.bottom - margin;
    const spaceAbove = rect.top - margin;
    const preferBelow = spaceBelow >= 150 || spaceBelow >= spaceAbove;
    const maxHeight = Math.max(120, Math.min(280, (preferBelow ? spaceBelow : spaceAbove) - 8));
    box.style.left = left + 'px';
    box.style.width = width + 'px';
    box.style.maxHeight = maxHeight + 'px';
    if (preferBelow) {
        box.style.top = (rect.bottom + 6) + 'px';
        box.style.bottom = 'auto';
    } else {
        box.style.top = 'auto';
        box.style.bottom = (window.innerHeight - rect.top + 6) + 'px';
    }
}

function applySelectedPlace(kind, lat, lng, label) {
    const input = document.getElementById(placeInputId(kind));
    placeSearchLock = true;
    if (input && label) input.value = label;
    placeSearchLock = false;
    hidePlaceSuggestions();
    clearLocationMessage();

    const latlng = { lat: Number(lat), lng: Number(lng) };
    if (!Number.isFinite(latlng.lat) || !Number.isFinite(latlng.lng)) {
        showLocationMessage('That place has no map point. Try another suggestion.', 'error');
        return;
    }

    suppressMapIdle = true;
    if (kind === 'pickup') {
        pickupLatLng = latlng;
        updateMarker('pickup', latlng);
        if (isGoogleMaps && map && map.setCenter) {
            map.setCenter(latlng);
            map.setZoom(16);
        }
    } else if (kind === 'stop') {
        stopLatLng = latlng;
        updateMarker('stop', latlng);
    } else {
        dropLatLng = latlng;
        updateMarker('drop', latlng);
    }
    if (pickupLatLng && dropLatLng) calculateRoute();
    setTimeout(() => { suppressMapIdle = false; }, 800);
}

function fillPlaceSuggestions(box, input, kind, items) {
    box.innerHTML = '';
    if (!items.length) {
        box.classList.add('d-none');
        return;
    }
    items.forEach((item) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'place-suggest-item';
        const title = document.createElement('span');
        title.className = 'place-suggest-title';
        title.textContent = item.title || '';
        btn.appendChild(title);
        if (item.subtitle) {
            const sub = document.createElement('span');
            sub.className = 'place-suggest-sub';
            sub.textContent = item.subtitle;
            btn.appendChild(sub);
        }
        btn.addEventListener('mousedown', (e) => e.preventDefault());
        btn.addEventListener('click', () => {
            hidePlaceSuggestions();
            if (item.lat != null && item.lng != null) {
                applySelectedPlace(kind, item.lat, item.lng, item.label || item.title);
                return;
            }
            if (item.placeId && isGoogleMaps && google.maps.places && map) {
                const service = new google.maps.places.PlacesService(map);
                service.getDetails({
                    placeId: item.placeId,
                    fields: ['formatted_address', 'geometry', 'name']
                }, (place, status) => {
                    if (status !== 'OK' || !place || !place.geometry || !place.geometry.location) {
                        showLocationMessage('Unable to open that place. Try another suggestion.', 'error');
                        return;
                    }
                    const chosen = place.formatted_address || place.name || item.title;
                    applySelectedPlace(kind, place.geometry.location.lat(), place.geometry.location.lng(), chosen);
                });
            }
        });
        box.appendChild(btn);
    });
    positionPlaceBox(input, box);
    box.classList.remove('d-none');
}

function searchPlaces(query, box, input, kind) {
    const bias = toPlainLatLng(pickupLatLng) || (map && typeof map.getCenter === 'function' ? toPlainLatLng(map.getCenter()) : null) || defaultMapLoc;

    if (isGoogleMaps && typeof google !== 'undefined' && google.maps && google.maps.places && google.maps.places.AutocompleteService) {
        const service = new google.maps.places.AutocompleteService();
        const request = {
            input: query,
            componentRestrictions: { country: 'in' }
        };
        if (bias) {
            request.location = new google.maps.LatLng(bias.lat, bias.lng);
            request.radius = 40000;
        }
        service.getPlacePredictions(request, (predictions, status) => {
            if (input.value.trim() !== query) return;
            if (status !== 'OK' || !predictions || !predictions.length) {
                box.classList.add('d-none');
                return;
            }
            fillPlaceSuggestions(box, input, kind, predictions.slice(0, 6).map((p) => ({
                title: p.structured_formatting && p.structured_formatting.main_text ? p.structured_formatting.main_text : p.description,
                subtitle: p.structured_formatting && p.structured_formatting.secondary_text ? p.structured_formatting.secondary_text : '',
                label: p.description,
                placeId: p.place_id
            })));
        });
        return;
    }

    const biasQuery = bias ? `&viewbox=${bias.lng - 0.6},${bias.lat + 0.6},${bias.lng + 0.6},${bias.lat - 0.6}&bounded=0` : '';
    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&countrycodes=in&limit=6&addressdetails=1' + biasQuery, {
        headers: { 'Accept-Language': 'en' }
    })
        .then((res) => res.ok ? res.json() : [])
        .then((data) => {
            if (input.value.trim() !== query) return null;
            const rows = Array.isArray(data) ? data : [];
            if (rows.length) return { nominatim: rows };
            return fetch('https://photon.komoot.io/api/?q=' + encodeURIComponent(query) + '&lat=' + bias.lat + '&lon=' + bias.lng + '&limit=6').then((res) => res.json());
        })
        .then((payload) => {
            if (!payload || input.value.trim() !== query) return;
            if (payload.nominatim) {
                fillPlaceSuggestions(box, input, kind, payload.nominatim.map((item) => {
                    const parts = String(item.display_name || '').split(',');
                    return {
                        title: (parts[0] || item.display_name || '').trim(),
                        subtitle: parts.slice(1, 4).join(',').trim(),
                        label: item.display_name,
                        lat: parseFloat(item.lat),
                        lng: parseFloat(item.lon)
                    };
                }));
                return;
            }
            const features = payload.features || [];
            fillPlaceSuggestions(box, input, kind, features.map((feature) => {
                const prop = feature.properties || {};
                const title = prop.name || prop.street || prop.city || query;
                const subtitle = [prop.street, prop.city || prop.town, prop.state].filter(Boolean).join(', ');
                const coords = feature.geometry && feature.geometry.coordinates ? feature.geometry.coordinates : [null, null];
                return {
                    title: title,
                    subtitle: subtitle,
                    label: [title, subtitle].filter(Boolean).join(', '),
                    lng: coords[0],
                    lat: coords[1]
                };
            }));
        })
        .catch(() => box.classList.add('d-none'));
}

function setupPlaceSearch(inputId, kind) {
    const input = document.getElementById(inputId);
    if (!input || input.dataset.placeSearch === '1') return;
    input.dataset.placeSearch = '1';
    input.setAttribute('autocomplete', 'off');

    const box = document.createElement('div');
    box.className = 'place-suggest d-none';
    box.setAttribute('role', 'listbox');
    document.body.appendChild(box);

    let debounceTimer;
    input.addEventListener('input', function () {
        if (placeSearchLock) return;
        if (kind === 'pickup') pickupLatLng = null;
        else if (kind === 'stop') stopLatLng = null;
        else dropLatLng = null;

        clearTimeout(debounceTimer);
        const query = this.value.trim();
        if (query.length < 2) {
            box.classList.add('d-none');
            return;
        }
        debounceTimer = setTimeout(() => searchPlaces(query, box, input, kind), 280);
    });

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const first = box.querySelector('.place-suggest-item');
            if (first) first.click();
        }
        if (event.key === 'Escape') box.classList.add('d-none');
    });

    window.addEventListener('resize', () => {
        if (!box.classList.contains('d-none')) positionPlaceBox(input, box);
    });
}

document.addEventListener('click', (event) => {
    if (event.target.closest && (event.target.closest('.place-suggest') || event.target.closest('#pickup-location, #drop-location, #stop-location'))) return;
    hidePlaceSuggestions();
});

const bookingSheet = document.getElementById('main-sheet');
if (bookingSheet) {
    bookingSheet.addEventListener('scroll', () => {
        const owner = document.activeElement;
        if (!owner || owner.tagName !== 'INPUT') return;
        document.querySelectorAll('.place-suggest').forEach((el) => {
            if (!el.classList.contains('d-none')) positionPlaceBox(owner, el);
        });
    });
}

function initAutocomplete() {
    setupPlaceSearch('pickup-location', 'pickup');
    setupPlaceSearch('drop-location', 'drop');
    setupPlaceSearch('stop-location', 'stop');
}

window.gm_authFailure = function() {
    console.warn('Google Maps auth failed. Showing the OpenStreetMap fallback.');
    const blocked = window.__mapsReferrerBlocked;
    showLocationMessage(
        blocked
            ? 'Google Maps blocked this website address. In Google Cloud, allow this site on the API key (local: http://127.0.0.1:8000/* plus your live domain). Pickup and destination still work on the map below.'
            : 'Google Maps could not start with the saved key. Pickup and destination still work on the map below.',
        'warning'
    );
    mapInitialized = false;
    isGoogleMaps = false;
    initLeaflet(defaultMapLoc);
};

function handlePlaceSelect(type) {
    if (!isGoogleMaps) return;

    const autocomplete = type === 'pickup' ? pickupAutocomplete : dropAutocomplete;
    if (!autocomplete) return;

    const place = autocomplete.getPlace();
    if (!place || !place.geometry || !place.geometry.location) {
        showLocationMessage('Unable to find this location. Please select a suggestion from the list.', 'error');
        return;
    }

    const latlng = {
        lat: place.geometry.location.lat(),
        lng: place.geometry.location.lng()
    };
    const address = place.formatted_address || place.name || '';

    holdMapIdle(1600);
    clearLocationMessage();

    if (type === 'pickup') {
        pickupLatLng = latlng;
        const pickupInput = document.getElementById('pickup-location');
        if (pickupInput) pickupInput.value = address;
        updateMarker('pickup', latlng);
        map.setCenter(latlng);
        map.setZoom(16);
    } else {
        dropLatLng = latlng;
        const dropInput = document.getElementById('drop-location');
        if (dropInput) dropInput.value = address;
        updateMarker('drop', latlng);
    }

    keepTypedLocation(document.getElementById(type === 'pickup' ? 'pickup-location' : (type === 'drop' ? 'drop-location' : 'stop-location')));

    if (pickupLatLng && dropLatLng) {
        calculateRoute();
    }
}

function onPlaceChanged() {
    // Legacy dual-read handler kept as no-op; place_changed uses handlePlaceSelect.
}

function updateMarker(type, latlng) {
    const lat = latlng.lat ? (typeof latlng.lat === 'function' ? latlng.lat() : latlng.lat) : latlng[0];
    const lng = latlng.lng ? (typeof latlng.lng === 'function' ? latlng.lng() : latlng.lng) : latlng[1];

    if (isGoogleMaps) {
        if (type === 'pickup') {
            if (!pickupMarker) {
                pickupMarker = new google.maps.Marker({
                    map: map,
                    icon: { path: google.maps.SymbolPath.CIRCLE, fillColor: '#cddc29', fillOpacity: 1, strokeColor: '#fff', strokeWeight: 2, scale: 8 }
                });
            }
            pickupMarker.setPosition({lat, lng});
            map.panTo({lat, lng});
        } else if (type === 'stop') {
            if (!stopMarker) {
                stopMarker = new google.maps.Marker({
                    map: map,
                    icon: { url: 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png' }
                });
            }
            stopMarker.setPosition({lat, lng});
        } else {
            if (!dropMarker) {
                dropMarker = new google.maps.Marker({ map: map, icon: { url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png' } });
            }
            dropMarker.setPosition({lat, lng});
        }
    } else {
        // Leaflet Markers
        if (type === 'pickup') {
            if (pickupMarker) map.removeLayer(pickupMarker);
            map.setView([lat, lng], 17);
        } else if (type === 'stop') {
            if (stopMarker) map.removeLayer(stopMarker);
            const yellowIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color:#ffc107;width:15px;height:15px;border-radius:50%;border:3px solid white;box-shadow:0 0 4px rgba(0,0,0,0.5)"></div>`,
                iconSize: [15, 15],
                iconAnchor: [7.5, 7.5]
            });
            stopMarker = L.marker([lat, lng], {icon: yellowIcon}).addTo(map);
        } else {
            if (dropMarker) map.removeLayer(dropMarker);
            dropMarker = L.marker([lat, lng]).addTo(map);
        }
    }
}

function rememberApproximateDistance(originFinal, destFinal) {
    const toRad = (deg) => deg * Math.PI / 180;
    const lat1 = Number(originFinal.lat);
    const lon1 = Number(originFinal.lng);
    const lat2 = Number(destFinal.lat);
    const lon2 = Number(destFinal.lng);
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = Math.sin(dLat / 2) ** 2
        + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;
    const km = (2 * 6371 * Math.asin(Math.min(1, Math.sqrt(a)))) * 1.3;
    lastCalculatedDistance = km;
    lastCalculatedDuration = Math.round(km * 120);
    updateFareByDistance(km);
    return km;
}

function calculateRoute() {
    const origin = toPlainLatLng(pickupLatLng);
    const destination = toPlainLatLng(dropLatLng);

    if (!destination) return;

    if (!origin && map) {
        const center = map.getCenter();
        if (isGoogleMaps) {
            pickupLatLng = { lat: center.lat(), lng: center.lng() };
        } else {
            pickupLatLng = [center.lat, center.lng];
        }
    }

    const originFinal = toPlainLatLng(pickupLatLng);
    const destFinal = toPlainLatLng(dropLatLng);
    if (!originFinal || !destFinal) return;

    const requestToken = ++routeRequestId;
    const distDisplay = document.getElementById('total-distance-display');
    if (distDisplay) distDisplay.innerText = 'Calculating route...';

    if (isGoogleMaps) {
        if (!directionsService || !directionsRenderer) {
            rememberApproximateDistance(originFinal, destFinal);
            showLocationMessage('Showing approximate route. Map routing is not ready.', 'warning');
            return;
        }

        const waypoints = [];
        const stopPlain = toPlainLatLng(stopLatLng);
        if (stopPlain) {
            waypoints.push({ location: stopPlain, stopover: true });
        }

        directionsService.route({
            origin: originFinal,
            destination: destFinal,
            waypoints: waypoints,
            travelMode: google.maps.TravelMode.DRIVING,
            provideRouteAlternatives: false
        }, (response, status) => {
            if (requestToken !== routeRequestId) return;

            if (status === 'OK' && response.routes && response.routes[0]) {
                directionsRenderer.setDirections(response);
                let totalDist = 0;
                let totalDuration = 0;
                response.routes[0].legs.forEach(leg => {
                    totalDist += (leg.distance && leg.distance.value ? leg.distance.value : 0) / 1000;
                    totalDuration += (leg.duration && leg.duration.value ? leg.duration.value : 0);
                });
                lastCalculatedDistance = totalDist;
                lastCalculatedDuration = totalDuration;
                updateFareByDistance(lastCalculatedDistance);

                const bounds = new google.maps.LatLngBounds();
                bounds.extend(originFinal);
                bounds.extend(destFinal);
                if (stopPlain) bounds.extend(stopPlain);
                suppressMapIdle = true;
                map.fitBounds(bounds, { top: 80, right: 40, bottom: 280, left: 40 });
                setTimeout(() => { suppressMapIdle = false; }, 900);
                clearLocationMessage();
            } else {
                console.warn('Directions failed:', status);
                rememberApproximateDistance(originFinal, destFinal);
                showLocationMessage('Showing approximate route. Road routing is temporarily unavailable.', 'warning');
            }
        });
    } else {
        // --- LEAFLET / OSRM LOGIC ---
        const pOrigin = [originFinal.lat, originFinal.lng];
        const pDest = [destFinal.lat, destFinal.lng];
        pickupLatLng = pOrigin;
        dropLatLng = pDest;
        
        // 1. Immediate Straight-Line Calculation (for instant feedback)
        const p1ll = L.latLng(pOrigin[0], pOrigin[1]);
        const p2ll = L.latLng(pDest[0], pDest[1]);
        let immediateDist = 0;
        
        if (stopLatLng) {
            const stopPlain = toPlainLatLng(stopLatLng);
            const stopll = L.latLng(stopPlain.lat, stopPlain.lng);
            immediateDist = ((p1ll.distanceTo(stopll) + stopll.distanceTo(p2ll)) / 1000) * 1.3;
        } else {
            immediateDist = (p1ll.distanceTo(p2ll) / 1000) * 1.3;
        }
        lastCalculatedDistance = immediateDist;
        lastCalculatedDuration = Math.round(immediateDist * 120); // rough seconds @ 30km/h
        updateFareByDistance(immediateDist);

        // 2. Road-based Routing using OSRM (Accurate background update)
        const p1 = pOrigin[1] + ',' + pOrigin[0];
        const p2 = pDest[1] + ',' + pDest[0];
        
        let routeUrl = `https://router.project-osrm.org/route/v1/driving/${p1};${p2}?overview=full&geometries=geojson`;
        if (stopLatLng) {
            const stopPlain = toPlainLatLng(stopLatLng);
            const pStop = stopPlain.lng + ',' + stopPlain.lat;
            routeUrl = `https://router.project-osrm.org/route/v1/driving/${p1};${pStop};${p2}?overview=full&geometries=geojson`;
        }
        
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 4000);

        fetch(routeUrl, { signal: controller.signal })
            .then(res => {
                clearTimeout(timeoutId);
                return res.json();
            })
            .then(data => {
                if (requestToken !== routeRequestId) return;
                if (data.routes && data.routes.length > 0) {
                    const route = data.routes[0];
                    const distKm = route.distance / 1000;
                    lastCalculatedDistance = distKm;
                    lastCalculatedDuration = route.duration || 0;
                    updateFareByDistance(distKm);

                    // Draw the actual road path and FIT map
                    const coordinates = route.geometry.coordinates.map(c => [c[1], c[0]]);
                    if (window.routeLine) map.removeLayer(window.routeLine);
                    window.routeLine = L.polyline(coordinates, {color: '#cddc29', weight: 6, opacity: 0.9}).addTo(map);
                    
                    suppressMapIdle = true;
                    map.fitBounds(window.routeLine.getBounds(), {padding: [70, 70]});
                    setTimeout(() => { suppressMapIdle = false; }, 900);
                    clearLocationMessage();
                }
            })
            .catch(err => {
                if (requestToken !== routeRequestId) return;
                console.warn("OSRM error or timeout, sticking with immediate distance.", err);
                if (window.routeLine) map.removeLayer(window.routeLine);
                const pathPoints = stopLatLng
                    ? [[originFinal.lat, originFinal.lng], [toPlainLatLng(stopLatLng).lat, toPlainLatLng(stopLatLng).lng], [destFinal.lat, destFinal.lng]]
                    : [[originFinal.lat, originFinal.lng], [destFinal.lat, destFinal.lng]];
                window.routeLine = L.polyline(pathPoints, {color: '#cddc29', weight: 5, dashArray: '10, 10'}).addTo(map);
                showLocationMessage('Showing approximate route. Road routing is temporarily unavailable.', 'warning');
            });
    }
}



function updateFareByDistance(km) {
    if (isNaN(km) || km === null || typeof km === 'undefined') {
        km = 0;
    }
    // Update the global distance display
    const distDisplay = document.getElementById('total-distance-display');
    if (distDisplay) {
        const mins = lastCalculatedDuration > 0
            ? Math.max(1, Math.round(lastCalculatedDuration / 60))
            : Math.max(1, Math.round(km * 2));
        distDisplay.innerText = 'Total Distance: ' + km.toFixed(1) + ' km • ~' + mins + ' min';
    }

    document.querySelectorAll('#ride-option-list .ride-option').forEach(option => {

        const base = parseFloat(option.dataset.base);
        let rate = parseFloat(option.dataset.rate);
        if (!Number.isFinite(base)) return;
        if (!Number.isFinite(rate)) rate = 0;
        
        const nameNode = option.querySelector('h6');
        const catName = nameNode ? nameNode.innerText.toLowerCase() : '';
        const isBikeOrAuto = catName.includes('bike') || catName.includes('auto') || catName.includes('cycle') || catName.includes('moto');
        const localRate = dehradunLocalRate(catName);
        if (localRate !== null) rate = localRate;
        
        // Removing global overrides to use the rate specific to the vehicle category
        if (!isBikeOrAuto) { if (acPreference === 'ac' && pricingSettings.ac_rate_per_km > 0) rate += pricingSettings.ac_rate_per_km; else if (acPreference === 'non-ac' && pricingSettings.non_ac_rate_per_km > 0) rate += pricingSettings.non_ac_rate_per_km; }

        let finalCatFare = base + (km * rate);
        if (pricingSettings.rain_surge_enabled) {
            let mult = pricingSettings.rain_surge_multiplier;
            if (mult > 10) mult = 1 + (mult / 100);
            finalCatFare = finalCatFare * mult;
        }
        if (pricingSettings.night_premium_enabled) {
            const hour = new Date().getHours();
            if (hour >= 0 && hour < 5) {
                finalCatFare = finalCatFare + pricingSettings.night_premium_amount;
            }
        }
        const catFare = finalCatFare.toFixed(2);
        
        // Update the per-km text display
        const rateDisplay = option.querySelector('.rate-per-km-text');
        if (rateDisplay) {
            rateDisplay.innerText = 'Rs. ' + Math.round(rate);
        }

        const fareDisplay = option.querySelector('.fare-display-item');

        if (fareDisplay) {
            fareDisplay.innerText = Number.isFinite(finalCatFare) && finalCatFare > 0 ? ('₹' + Number(finalCatFare).toFixed(2)) : '—';
        }

        // Recalculate drop time dynamically
        const dropDisplay = option.querySelector('.drop-time-display');
        if (dropDisplay) {
            const waitMin = parseInt(dropDisplay.dataset.wait) || 5;
            const travelMin = lastCalculatedDuration > 0
                ? Math.max(1, Math.round(lastCalculatedDuration / 60))
                : (Math.round(km * 2) || 10);
            const totalMin = waitMin + travelMin;
            
            const dropDate = new Date();
            dropDate.setMinutes(dropDate.getMinutes() + totalMin);
            
            let hours = dropDate.getHours();
            const minutes = dropDate.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12;
            dropDisplay.innerText = `${hours}:${minutes} ${ampm}`;
        }

        // If this is the currently active option, update the global baseFare
        if (option.classList.contains('active')) {
            baseFare = parseFloat(option.dataset.base) || 0;
        }
    });
    updateTotal();
}

function selectRide(element) {
    document.querySelectorAll('#ride-option-list .ride-option').forEach(o => o.classList.remove('active'));
    element.classList.add('active');
    
    baseFare = parseFloat(element.dataset.base) || 0;
    
    const nameNode = element.querySelector('h6');
    const rideName = nameNode && nameNode.firstChild ? nameNode.firstChild.textContent.trim() : 'Ride';
    document.getElementById('selected-ride-name').innerText = rideName;

    updateTotal();
}

function setPaymentMethod(method) {
    selectedPaymentMethod = method;
    document.getElementById('selected-payment-method').value = method;
    
    // UI update
    const display = document.getElementById('current-payment-display');
    if (method === 'cash') display.innerText = 'Cash';
    else if (method === 'wallet') display.innerText = 'Wallet';
    else if (method === 'online') display.innerText = 'Online';
}

function expandBookingSheet() {
    const map = document.getElementById('main-content');
    const sheet = document.getElementById('main-sheet');
    if (map) map.style.minHeight = '12dvh';
    if (sheet) {
        const nav = document.querySelector('.rider-bottom-nav');
        const top = sheet.getBoundingClientRect().top;
        const limit = nav ? nav.getBoundingClientRect().top : window.innerHeight;
        const height = Math.max(320, Math.floor(limit - top));
        sheet.style.height = height + 'px';
        sheet.style.maxHeight = height + 'px';
        sheet.classList.add('sheet-picking');
        sheet.scrollTop = 0;
    }
}

function restoreBookingSheet() {
    const map = document.getElementById('main-content');
    const sheet = document.getElementById('main-sheet');
    if (map) map.style.minHeight = '';
    if (sheet) {
        sheet.style.height = '';
        sheet.style.maxHeight = '';
        sheet.classList.remove('sheet-picking');
    }
}

function updateRideOptionsVisibility() {
    document.querySelectorAll('#ride-option-list .ride-option').forEach(opt => {
        let hide = false;
        
        // Freight vehicles stay off the ride and rental lists. AC only changes the fare, not which vehicles are shown.
        if (currentService === 'ride' || currentService === 'rental') {
            if (opt.classList.contains('freight-cat')) hide = true;
        }

        if (hide) {
            opt.classList.add('d-none');
            opt.classList.remove('d-flex');
        } else {
            opt.classList.remove('d-none');
            opt.classList.add('d-flex');
        }
    });

    // Auto-select first visible option if current is hidden
    const activeOption = document.querySelector('#ride-option-list .ride-option.active');
    if (!activeOption || activeOption.classList.contains('d-none')) {
        const firstVisible = Array.from(document.querySelectorAll('#ride-option-list .ride-option')).find(opt => !opt.classList.contains('d-none'));
        if (firstVisible) {
            selectRide(firstVisible);
        }
    }
}

function setService(type) {
    currentService = type;
    
    // UI Update Tabs
    document.querySelectorAll('.scroller-x .btn').forEach(btn => {
        btn.classList.replace('btn-brand', 'btn-light');
        btn.classList.add('text-dark');
        btn.classList.remove('text-secondary');
        if (btn.getAttribute('role') === 'tab') {
            btn.setAttribute('aria-selected', 'false');
        }
    });
    document.getElementById(`tab-${type}`).classList.replace('btn-light', 'btn-brand');
    document.getElementById(`tab-${type}`).classList.remove('text-dark');
    const activeTab = document.getElementById(`tab-${type}`);
    if (activeTab && activeTab.getAttribute('role') === 'tab') {
        activeTab.setAttribute('aria-selected', 'true');
    }

    // Toggle Fields
    document.getElementById('parcel-fields').classList.toggle('d-none', type !== 'parcel');
    document.getElementById('rental-fields').classList.toggle('d-none', type !== 'rental');
    
    updateRideOptionsVisibility();

    // Update Titles
    const btn = document.getElementById('search-btn');
    if (type === 'parcel') btn.innerText = 'Search Delivery Partners';
    else if (type === 'freight') btn.innerText = 'Search Freight Trucks';
    else btn.innerText = 'Search Rides';

    // Update Searching Icon
    const icon = document.querySelector('#service-icon i');
    const title = document.getElementById('search-title');
    const desc = document.getElementById('search-desc');
    
    if (type === 'parcel') {
        icon.className = 'bi bi-box-seam-fill text-brand display-4';
        title.innerText = 'Finding delivery partners';
        desc.innerText = 'Connecting with nearby couriers...';
    } else if (type === 'freight') {
        icon.className = 'bi bi-truck text-brand display-4';
        title.innerText = 'Finding freight trucks';
        desc.innerText = 'Matching with heavy loaders...';
    } else {
        icon.className = 'bi bi-car-front-fill text-brand display-4';
        title.innerText = 'Finding top drivers';
        desc.innerText = 'Connecting with nearby taxis...';
    }
}

function selectPackage(element, price) {
    document.querySelectorAll('.package-capsule').forEach(p => p.classList.remove('active'));
    element.classList.add('active');
}

function applyCoupon() {
    const code = document.getElementById('coupon-code').value.toUpperCase();
    if (code === 'WELCOME50') {
        discount = 50;
        alert('Promo code applied: Rs. 50 OFF!');
        updateTotal();
    } else {
        alert('Invalid promo code');
    }
}

function updateTotal() {
    let finalBase = baseFare;
    let weightAdder = 0;
    let distanceKm = lastCalculatedDistance || 0;
    
    // Calculate distance-based part for breakdown
    const activeOption = document.querySelector('#ride-option-list .ride-option.active:not(.d-none)');
    let ratePerKm = 0;
    if (activeOption) {
        let rate = parseFloat(activeOption.dataset.rate);
        if (!Number.isFinite(rate)) rate = 0;
        const nameNode = activeOption.querySelector('h6');
        const catName = nameNode ? nameNode.innerText.toLowerCase() : '';
        const isBikeOrAuto = catName.includes('bike') || catName.includes('auto') || catName.includes('cycle') || catName.includes('moto');
        const localRate = dehradunLocalRate(catName);
        if (localRate !== null) rate = localRate;
        
        if (!isBikeOrAuto) { if (acPreference === 'ac' && pricingSettings.ac_rate_per_km > 0) rate += pricingSettings.ac_rate_per_km; else if (acPreference === 'non-ac' && pricingSettings.non_ac_rate_per_km > 0) rate += pricingSettings.non_ac_rate_per_km; }
        ratePerKm = rate;
    }

    const distFare = distanceKm * ratePerKm;
    const subTotal = finalBase + distFare;

    // Update Breakdown UI
    document.getElementById('breakdown-base').innerText = `Rs. ${finalBase.toFixed(2)}`;
    document.getElementById('breakdown-km').innerText = distanceKm.toFixed(1);
    document.getElementById('breakdown-dist-fare').innerText = `Rs. ${distFare.toFixed(2)}`;
    document.getElementById('breakdown-total').innerText = `Rs. ${subTotal.toFixed(2)}`;
    
    // Update the "Math" breakdown next to Best Price - Showing the full formula
    document.getElementById('calculation-breakdown').innerHTML = `Rs. ${finalBase.toFixed(0)} + (${distanceKm.toFixed(1)}km &times; Rs. ${ratePerKm.toFixed(0)}) = <span class="text-brand">Rs. ${subTotal.toFixed(2)}</span>`;

    if (currentService === 'parcel') {
        const weight = parseFloat(document.getElementById('parcel-weight').value) || 0;
        weightAdder = weight * 10;
    } 

    let total = Math.max(0, (subTotal + weightAdder) - discount);

    // Apply Dynamic Surge & Premium
    if (pricingSettings.rain_surge_enabled) {
        let mult = pricingSettings.rain_surge_multiplier;
        if (mult > 10) mult = 1 + (mult / 100);
        total = total * mult;
    }

    if (pricingSettings.night_premium_enabled) {
        const hour = new Date().getHours();
        if (hour >= 0 && hour < 5) {
            total = total + pricingSettings.night_premium_amount;
        }
    }

    estimatedRideFare = Number.isFinite(total) && total > 0 ? total : 0;
    refreshCustomerOffer();
}

function selectCustomerOffer(amount, button) {
    customerOfferExtra = amount;
    const custom = document.getElementById('custom-offer');
    if (custom) custom.value = '';
    document.querySelectorAll('[data-offer-extra]').forEach(function (el) {
        const selected = Number(el.getAttribute('data-offer-extra')) === Number(amount);
        el.classList.toggle('btn-brand', selected);
        el.classList.toggle('text-dark', selected);
        el.classList.toggle('btn-outline-dark', !selected);
        el.setAttribute('aria-pressed', selected ? 'true' : 'false');
    });
    if (button) button.blur();
    refreshCustomerOffer();
}

function setCustomCustomerOffer() {
    const custom = document.getElementById('custom-offer');
    const raw = custom ? parseFloat(custom.value) : NaN;
    document.querySelectorAll('[data-offer-extra]').forEach(function (el) {
        el.classList.remove('btn-brand', 'text-dark');
        el.classList.add('btn-outline-dark');
        el.setAttribute('aria-pressed', 'false');
    });
    customerOfferExtra = (custom && custom.value !== '' && Number.isFinite(raw) && raw > 0) ? raw : 0;
    refreshCustomerOffer();
}

function refreshCustomerOffer() {
    const estimate = estimatedRideFare;
    const extra = Number.isFinite(customerOfferExtra) && customerOfferExtra > 0 ? customerOfferExtra : 0;
    const ready = Number.isFinite(estimate) && estimate > 0;
    const finalAmount = ready ? estimate + extra : null;
    const estEl = document.getElementById('estimated-fare');
    const finalEl = document.getElementById('final-offer');
    const fareEl = document.getElementById('total-fare');
    if (estEl) estEl.textContent = ready ? ('₹' + estimate.toFixed(2)) : '—';
    if (finalEl) finalEl.textContent = finalAmount === null ? '—' : ('₹' + finalAmount.toFixed(2));
    if (fareEl) fareEl.innerText = finalAmount === null ? 'Fare unavailable' : ('₹' + finalAmount.toFixed(2));
}

function formatInrFare(amount) {
    const n = Number(amount);
    return Number.isFinite(n) && n > 0 ? ('₹' + n.toFixed(2)) : 'Fare unavailable';
}

function openSchedule() {
    const modal = document.getElementById('schedule-modal');
    if (!modal) return;
    modal.classList.remove('d-none');
    modal.setAttribute('aria-hidden', 'false');
    const closeBtn = modal.querySelector('button[aria-label]');
    if (closeBtn) closeBtn.focus();
}
function closeSchedule() {
    const modal = document.getElementById('schedule-modal');
    if (!modal) return;
    modal.classList.add('d-none');
    modal.setAttribute('aria-hidden', 'true');
}

function setSchedule() {
    const time = document.getElementById('schedule-time').value;
    if (!time) return alert('Please select a time');
    scheduledTime = time;
    alert(`Scheduled for: ${new Date(time).toLocaleString()}`);
    closeSchedule();
}

/* Location Permission Modal Handler */
function openLocationModal() {
    const modal = document.getElementById('location-permission-modal');
    if (!modal) return;
    modal.classList.remove('d-none');
    modal.setAttribute('aria-hidden', 'false');
    checkPermissionStatus();
}

function closeLocationModal() {
    const modal = document.getElementById('location-permission-modal');
    if (!modal) return;
    modal.classList.add('d-none');
    modal.setAttribute('aria-hidden', 'true');
}

document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    const schedule = document.getElementById('schedule-modal');
    const location = document.getElementById('location-permission-modal');
    if (schedule && !schedule.classList.contains('d-none')) {
        closeSchedule();
    }
    if (location && !location.classList.contains('d-none')) {
        closeLocationModal();
    }
});

function checkPermissionStatus() {
    if (navigator.permissions && navigator.permissions.query) {
        navigator.permissions.query({ name: 'geolocation' }).then(function(result) {
            updatePermissionModalStatus(result.state);
            result.onchange = function() {
                updatePermissionModalStatus(result.state);
            };
        }).catch(err => {
            updatePermissionModalStatus('prompt');
        });
    } else {
        updatePermissionModalStatus('prompt');
    }
}

function updatePermissionModalStatus(state) {
    const iconContainer = document.getElementById('permission-status-icon');
    const titleEl = document.getElementById('permission-status-title');
    const descEl = document.getElementById('permission-status-desc');
    const guideEl = document.getElementById('permission-guide-container');
    const allowBtn = document.getElementById('modal-allow-btn');

    if (state === 'granted') {
        iconContainer.innerHTML = `<div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; animation: pulse-success 2s infinite;"><i class="bi bi-check-circle-fill fs-1"></i></div>`;
        titleEl.innerText = "Location is active";
        titleEl.className = "fw-bold mb-1 text-success";
        descEl.innerText = "Your browser is successfully sharing location coordinates.";
        guideEl.classList.add('d-none');
        allowBtn.innerHTML = `<i class="bi bi-check2-all me-1"></i> Location Enabled`;
        allowBtn.disabled = true;
    } else if (state === 'denied') {
        iconContainer.innerHTML = `<div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; animation: pulse-danger 2s infinite;"><i class="bi bi-shield-slash-fill fs-1"></i></div>`;
        titleEl.innerText = "Location permission blocked";
        titleEl.className = "fw-bold mb-1 text-danger";
        descEl.innerText = "Location permission is required to use your current location. You can still search pickup manually.";
        guideEl.classList.remove('d-none');
        allowBtn.innerHTML = `<i class="bi bi-arrow-clockwise me-1"></i> Try Re-detecting`;
        allowBtn.disabled = false;
    } else {
        iconContainer.innerHTML = `<div class="bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; animation: pulse-warning 2s infinite;"><i class="bi bi-geo-alt-fill fs-1 text-white"></i></div>`;
        titleEl.innerText = "Allow location access";
        titleEl.className = "fw-bold mb-1 text-warning";
        descEl.innerText = "We need location access to set your exact pickup point on the map.";
        guideEl.classList.add('d-none');
        allowBtn.innerHTML = `<i class="bi bi-crosshair me-1"></i> Allow & Detect Location`;
        allowBtn.disabled = false;
    }
}

function checkAndRequestLocation() {
    closeLocationModal();
    detectLocation();
}

function clearLocationCache() {
    localStorage.removeItem('location_allowed');
    pickupLatLng = null;
    document.getElementById('pickup-location').value = "";
    document.getElementById('pickup-location').placeholder = "Enter pickup point";
    alert("Location settings cleared!");
    checkPermissionStatus();
}

/* Map Picker Controllers (Rapido-Style) */
let activeMapPickerType = null;

function startMapPicker(type) {
    activeMapPickerType = type;
    
    // Hide the booking sheet so map is fully visible
    document.getElementById('main-sheet').classList.add('d-none');
    
    // Show the picker floating panel
    const panel = document.getElementById('map-picker-panel');
    panel.classList.remove('d-none');
    
    // Update labels and styles based on selection type
    const badge = document.getElementById('map-picker-badge');
    const title = document.getElementById('map-picker-title');
    const pinLabel = document.querySelector('#center-pin .pin-label');
    const pinIcon = document.querySelector('#center-pin .pin-icon i');

    if (type === 'pickup') {
        badge.innerText = "SET PICKUP";
        badge.style.setProperty('background-color', 'var(--primary-color)', 'important');
        badge.style.setProperty('color', '#000', 'important');
        title.innerText = "Select Pickup Location";
        if (pinLabel) pinLabel.innerText = "Pickup Point";
        if (pinIcon) {
            pinIcon.className = "bi bi-geo-alt-fill text-brand";
            pinIcon.style.color = "";
        }
    } else if (type === 'stop') {
        badge.innerText = "SET STOP";
        badge.style.setProperty('background-color', '#ffc107', 'important');
        badge.style.setProperty('color', '#000', 'important');
        title.innerText = "Select Stop Location";
        if (pinLabel) pinLabel.innerText = "Stop Point";
        if (pinIcon) {
            pinIcon.className = "bi bi-geo-alt-fill text-warning";
            pinIcon.style.setProperty('color', '#ffc107', 'important');
        }
    } else {
        badge.innerText = "SET DROP";
        badge.style.setProperty('background-color', '#dc3545', 'important');
        badge.style.setProperty('color', '#fff', 'important');
        title.innerText = "Select Drop Location";
        if (pinLabel) pinLabel.innerText = "Drop Point";
        if (pinIcon) {
            pinIcon.className = "bi bi-geo-alt-fill text-danger";
            pinIcon.style.setProperty('color', '#dc3545', 'important');
        }
    }

    const center = toPlainLatLng(map.getCenter());
    if (!center) return;
    if (isGoogleMaps) reverseGeocodeGoogle(center, 'map-picker-address');
    else reverseGeocode(center.lat, center.lng, 'map-picker-address');
}

function stopMapPicker() {
    activeMapPickerType = null;
    document.getElementById('map-picker-panel').classList.add('d-none');
    document.getElementById('main-sheet').classList.remove('d-none');
    
    // Restore default center pin styles
    const pinLabel = document.querySelector('#center-pin .pin-label');
    const pinIcon = document.querySelector('#center-pin .pin-icon i');
    if (pinLabel) pinLabel.innerText = "Pickup Point";
    if (pinIcon) {
        pinIcon.className = "bi bi-geo-alt-fill text-brand";
        pinIcon.style.color = "";
    }
}

function confirmMapPickerSelection() {
    const center = toPlainLatLng(map.getCenter());
    if (!center) return;
    const rawAddress = (document.getElementById('map-picker-address').textContent || '').trim();
    const pending = !rawAddress || /detecting|finding place|unable to resolve/i.test(rawAddress);
    const address = pending ? `${center.lat.toFixed(5)}, ${center.lng.toFixed(5)}` : rawAddress;
    const latlng = { lat: center.lat, lng: center.lng };

    if (activeMapPickerType === 'pickup') {
        pickupLatLng = latlng;
        document.getElementById('pickup-location').value = address;
        updateMarker('pickup', latlng);
    } else if (activeMapPickerType === 'stop') {
        stopLatLng = latlng;
        document.getElementById('stop-location').value = address;
        updateMarker('stop', latlng);
        calculateRoute();
    } else if (activeMapPickerType === 'drop') {
        dropLatLng = latlng;
        document.getElementById('drop-location').value = address;
        updateMarker('drop', latlng);
        calculateRoute();
    }

    stopMapPicker();
}

function addStopLocation() {
    document.getElementById('stop-location-container').classList.remove('d-none');
    document.getElementById('pickup-to-stop-line').classList.remove('d-none');
    document.getElementById('pickup-to-drop-line').classList.add('d-none');
    document.getElementById('add-stop-btn').classList.add('d-none');
}

function removeStopLocation() {
    stopLatLng = null;
    document.getElementById('stop-location').value = "";
    if (stopMarker) {
        if (isGoogleMaps) stopMarker.setMap(null);
        else map.removeLayer(stopMarker);
        stopMarker = null;
    }
    
    document.getElementById('stop-location-container').classList.add('d-none');
    document.getElementById('pickup-to-stop-line').classList.add('d-none');
    document.getElementById('pickup-to-drop-line').classList.remove('d-none');
    document.getElementById('add-stop-btn').classList.remove('d-none');
    
    calculateRoute();
}

async function detectLocation() {
    const pickupInput = document.getElementById('pickup-location');
    const detectIcon = document.querySelector('#detect-btn i');
    if (!pickupInput) return;

    if (!navigator.geolocation) {
        showLocationMessage('Location is not supported in this browser. Please search for your pickup location manually.', 'error');
        return;
    }

    isDetectingLocation = true;
    suppressMapIdle = true;
    showLocationMessage('Getting your location...', 'muted');
    pickupInput.value = 'Getting your location...';
    pickupInput.dataset.lastRequest = Date.now();
    if (detectIcon) detectIcon.classList.add('bi-spin');

    const finishDetect = () => {
        isDetectingLocation = false;
        if (detectIcon) detectIcon.classList.remove('bi-spin');
        setTimeout(() => { suppressMapIdle = false; }, 1000);
    };

    const applyPosition = (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        const accuracy = position.coords.accuracy;
        const latlngObj = { lat, lng: lon };
        const latlng = isGoogleMaps ? latlngObj : [lat, lon];

        pickupLatLng = latlng;
        pickupInput.dataset.lastRequest = Date.now();

        if (accuracy && accuracy > 500) {
            showLocationMessage('Location accuracy is low. You can drag the map or search to refine pickup.', 'warning');
        } else {
            showLocationMessage('Current location set as pickup.', 'success');
        }

        if (isGoogleMaps) {
            if (map) {
                map.setCenter(latlngObj);
                map.setZoom(17);
            }
            updateMarker('pickup', latlngObj);
            reverseGeocodeGoogle(latlngObj, 'pickup-location', () => {
                if (dropLatLng) calculateRoute();
                finishDetect();
            });
            // Safety timeout if geocoder hangs
            setTimeout(finishDetect, 8000);
        } else {
            if (map) map.setView([lat, lon], 18);
            updateMarker('pickup', latlng);
            reverseGeocode(lat, lon, 'pickup-location');
            if (dropLatLng) calculateRoute();
            finishDetect();
        }

        try { localStorage.setItem('location_allowed', '1'); } catch (e) {}
        closeLocationModal();
    };

    const handleError = (error) => {
        finishDetect();
        pickupInput.placeholder = 'Search pickup or use current location';

        // Do not silently pretend city-center is the rider's GPS location.
        let message = 'Unable to detect your location. Please search for your pickup location manually.';
        if (error) {
            if (error.code === 1) {
                message = 'Location permission is required to use your current location.';
            } else if (error.code === 2) {
                message = 'GPS is unavailable. Please search for your pickup location manually.';
            } else if (error.code === 3) {
                message = 'Location request timed out. Please try again or search manually.';
            }
        }

        showLocationMessage(message, 'error');
        if (!pickupInput.value || pickupInput.value === 'Getting your location...' || pickupInput.value === 'Finding exact house...') {
            pickupInput.value = '';
        }

        console.warn('Geolocation failed:', error);
        closeLocationModal();
        updatePermissionModalStatus(error && error.code === 1 ? 'denied' : 'prompt');
    };

    // 1. Try high-accuracy GPS first
    navigator.geolocation.getCurrentPosition(
        applyPosition,
        (error) => {
            // Permission denied / unsupported: do not retry with low accuracy as if granted
            if (error && error.code === 1) {
                handleError(error);
                return;
            }
            console.warn('High-accuracy geolocation failed; retrying with network location...', error);
            navigator.geolocation.getCurrentPosition(
                applyPosition,
                handleError,
                { enableHighAccuracy: false, timeout: 10000, maximumAge: 15000 }
            );
        },
        { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
    );
}

function geocodeLeaflet(type) {
    if (isGoogleMaps) return;
    const input = document.getElementById(type + '-location');
    if (!input.value) return;

    fetch(`https://nominatim.openstreetmap.org/search?format=json&email=support@doonspedo.com&q=${encodeURIComponent(input.value)}&countrycodes=in&limit=1`)
        .then(res => {
            if (!res.ok) throw new Error("Nominatim status error");
            return res.json();
        })
        .then(data => {
            if (data && data.length > 0) {
                const latlng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                if (type === 'pickup') pickupLatLng = latlng;
                else if (type === 'stop') stopLatLng = latlng;
                else dropLatLng = latlng;
                updateMarker(type, latlng);
                calculateRoute();
            } else {
                throw new Error("No OSM results");
            }
        })
        .catch(err => {
            console.warn("Nominatim direct geocode failed, trying Photon fallback...", err);
            fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(input.value)}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.features && data.features.length > 0) {
                        const geom = data.features[0].geometry.coordinates;
                        const latlng = [geom[1], geom[0]]; // Photon returns [lon, lat]
                        if (type === 'pickup') pickupLatLng = latlng;
                        else if (type === 'stop') stopLatLng = latlng;
                        else dropLatLng = latlng;
                        updateMarker(type, latlng);
                        calculateRoute();
                    }
                })
                .catch(e => console.error("Photon direct geocode failed too", e));
        });
}

function setACPreference(pref) {
    acPreference = pref;
    const btnAc = document.getElementById('btn-ac-pref');
    const btnNonAc = document.getElementById('btn-non-ac-pref');

    if (pref === 'ac') {
        btnAc.classList.add('btn-brand');
        btnAc.classList.remove('btn-light', 'text-secondary');
        btnNonAc.classList.add('btn-light', 'text-secondary');
        btnNonAc.classList.remove('btn-brand');
    } else {
        btnNonAc.classList.add('btn-brand');
        btnNonAc.classList.remove('btn-light', 'text-secondary');
        btnAc.classList.add('btn-light', 'text-secondary');
        btnAc.classList.remove('btn-brand');
    }

    updateRideOptionsVisibility();
    if (lastCalculatedDistance > 0) {
        updateFareByDistance(lastCalculatedDistance);
    } else {
        updateTotal();
    }
}

async function startSearching() {
    keepTypedLocation(document.getElementById('pickup-location'));
    keepTypedLocation(document.getElementById('drop-location'));
    keepTypedLocation(document.getElementById('stop-location'));
    const dropInput = document.getElementById('drop-location');
    const dropText = dropInput.value.trim();
    const pickupInput = document.getElementById('pickup-location');
    const pickupText = pickupInput.value.trim();
    const stopInput = document.getElementById('stop-location');
    const stopText = stopInput ? stopInput.value.trim() : '';
    
    if (!pickupText) {
        showLocationMessage('Please set a pickup location first.', 'error');
        return alert('Please set a pickup location');
    }
    if(!dropText && currentService !== 'rental') return alert('Please enter destination');

    showLocationMessage('Finding available rides...', 'muted');
    
    // 1. Geocode Pickup if missing or input was manually edited
    if (!pickupLatLng && pickupText) {
        if (isGoogleMaps) {
            try {
                const geocoder = new google.maps.Geocoder();
                pickupLatLng = await new Promise((resolve, reject) => {
                    geocoder.geocode({ address: pickupText }, (results, status) => {
                        if (status === 'OK' && results[0]) resolve(results[0].geometry.location);
                        else reject(status);
                    });
                });
                updateMarker('pickup', pickupLatLng);
            } catch (e) { console.error("Google pickup geocode failed", e); }
        } else {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&email=support@doonspedo.com&q=${encodeURIComponent(pickupText)}&countrycodes=in&limit=1`);
                const data = await res.json();
                if (data && data.length > 0) {
                    pickupLatLng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                    updateMarker('pickup', pickupLatLng);
                } else {
                    throw new Error("No OSM pickup results");
                }
            } catch (e) {
                console.warn("OSM pickup geocode failed, trying Photon...", e);
                try {
                    const res = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(pickupText)}&limit=1`);
                    const data = await res.json();
                    if (data && data.features && data.features.length > 0) {
                        const geom = data.features[0].geometry.coordinates;
                        pickupLatLng = [geom[1], geom[0]];
                        updateMarker('pickup', pickupLatLng);
                    }
                } catch (ph) { console.error("Photon pickup geocode failed too", ph); }
            }
        }
    }

    // Fallback: If pickupLatLng is still missing, use current map center (map-drag pickup)
    if (!toPlainLatLng(pickupLatLng) && map) {
        const center = map.getCenter();
        pickupLatLng = isGoogleMaps
            ? { lat: center.lat(), lng: center.lng() }
            : [center.lat, center.lng];
    }

    if (!toPlainLatLng(pickupLatLng)) {
        showLocationMessage('Unable to find this location. Please search pickup again.', 'error');
        return alert('Unable to resolve pickup location. Please search or use current location.');
    }

    // 2. Geocode Stop if stopText is present but stopLatLng is missing
    if (stopText && !stopLatLng) {
        if (isGoogleMaps) {
            try {
                const geocoder = new google.maps.Geocoder();
                stopLatLng = await new Promise((resolve, reject) => {
                    geocoder.geocode({ address: stopText }, (results, status) => {
                        if (status === 'OK' && results[0]) resolve(results[0].geometry.location);
                        else reject(status);
                    });
                });
                updateMarker('stop', stopLatLng);
            } catch (e) { console.error("Google stop geocode failed", e); }
        } else {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&email=support@doonspedo.com&q=${encodeURIComponent(stopText)}&countrycodes=in&limit=1`);
                const data = await res.json();
                if (data && data.length > 0) {
                    stopLatLng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                    updateMarker('stop', stopLatLng);
                } else {
                    throw new Error("No OSM stop results");
                }
            } catch (e) {
                console.warn("OSM stop geocode failed, trying Photon...", e);
                try {
                    const res = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(stopText)}&limit=1`);
                    const data = await res.json();
                    if (data && data.features && data.features.length > 0) {
                        const geom = data.features[0].geometry.coordinates;
                        stopLatLng = [geom[1], geom[0]];
                        updateMarker('stop', stopLatLng);
                    }
                } catch (ph) { console.error("Photon stop geocode failed too", ph); }
            }
        }
    }

    // 3. Geocode Dropoff if dropLatLng is missing
    if (!dropLatLng && dropText && currentService !== 'rental') {
        if (isGoogleMaps) {
            try {
                const geocoder = new google.maps.Geocoder();
                dropLatLng = await new Promise((resolve, reject) => {
                    geocoder.geocode({ address: dropText }, (results, status) => {
                        if (status === 'OK' && results[0]) resolve(results[0].geometry.location);
                        else reject(status);
                    });
                });
                updateMarker('drop', dropLatLng);
            } catch (e) { console.error("Google drop geocode failed", e); }
        } else {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&email=support@doonspedo.com&q=${encodeURIComponent(dropText)}&countrycodes=in&limit=1`);
                const data = await res.json();
                if (data && data.length > 0) {
                    dropLatLng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                    updateMarker('drop', dropLatLng);
                } else {
                    throw new Error("No OSM drop results");
                }
            } catch (e) {
                console.warn("OSM drop geocode failed, trying Photon fallback...", e);
                try {
                    const res = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(dropText)}&limit=1`);
                    const data = await res.json();
                    if (data && data.features && data.features.length > 0) {
                        const geom = data.features[0].geometry.coordinates;
                        dropLatLng = [geom[1], geom[0]];
                        updateMarker('drop', dropLatLng);
                    }
                } catch (phErr) {
                    console.error("Photon drop geocode fallback failed too", phErr);
                }
            }
        }
    }

    // Final check: Calculate route if we have both points
    if (!toPlainLatLng(dropLatLng) && currentService !== 'rental') {
        showLocationMessage('Unable to find this destination. Please select a drop suggestion.', 'error');
        return alert('Unable to resolve destination. Please select a place from autocomplete.');
    }

    if (pickupLatLng && dropLatLng) {
        calculateRoute();
    } else if (currentService !== 'rental') {
        showLocationMessage('Unable to calculate route. Pickup and drop are required.', 'error');
        return;
    }
    
    clearLocationMessage();
    document.getElementById('location-sheet').classList.add('d-none');
    document.getElementById('center-pin').classList.add('d-none'); 
    document.getElementById('searching-sheet').classList.remove('d-none');
    
    // Perform a second calculation during the searching animation just to be safe
    setTimeout(() => { if (pickupLatLng && dropLatLng) calculateRoute(); }, 1000);

    setTimeout(() => {
        document.getElementById('searching-sheet').classList.add('d-none');
        document.getElementById('ride-options-sheet').classList.remove('d-none');
        expandBookingSheet();
        
        // Hide service tabs to save space
        const serviceTabs = document.getElementById('service-tabs-container');
        if (serviceTabs) serviceTabs.classList.add('d-none');
        
        // Final force calculation when sheet opens
        if (pickupLatLng && dropLatLng) calculateRoute();

        // Auto-select first visible option
        const firstVisible = document.querySelector(`.ride-option:not(.d-none)`);
        if (firstVisible) firstVisible.click();
        
        if (currentService === 'parcel') {
            document.getElementById('options-title').innerText = 'Select Delivery Vehicle';
        } else {
            document.getElementById('options-title').innerText = 'Select ride category';
        }
    }, 2000);
}

function cancelSearch() {
    restoreBookingSheet();
    document.getElementById('searching-sheet').classList.add('d-none');
    document.getElementById('location-sheet').classList.remove('d-none');
    document.getElementById('center-pin').classList.remove('d-none'); // Show pin again
    
    // Show service tabs again
    const serviceTabs = document.getElementById('service-tabs-container');
    if (serviceTabs) serviceTabs.classList.remove('d-none');
}

function goBackToLocation() {
    restoreBookingSheet();
    document.getElementById('ride-options-sheet').classList.add('d-none');
    document.getElementById('location-sheet').classList.remove('d-none');
    document.getElementById('center-pin').classList.remove('d-none'); // Show pin again
    
    // Show service tabs again
    const serviceTabs = document.getElementById('service-tabs-container');
    if (serviceTabs) serviceTabs.classList.remove('d-none');
}

let currentBookingId = null;
let bidPollingInterval = null;
let isSubmittingBooking = false;

function confirmBooking() {
    if (isSubmittingBooking) return;

    keepTypedLocation(document.getElementById('pickup-location'));
    keepTypedLocation(document.getElementById('drop-location'));
    const pickup = (document.getElementById('pickup-location').value || '').trim();
    const drop = (document.getElementById('drop-location').value || '').trim();
    const fareText = document.getElementById('total-fare')?.innerText || '';
    const fareMatch = fareText.match(/\d+(?:\.\d+)?/);
    const totalFare = fareMatch ? parseFloat(fareMatch[0]) : 0;
    const activeCategory = document.querySelector('.ride-option.active:not(.d-none)');
    const catId = activeCategory ? activeCategory.getAttribute('data-id') : null;

    const pickupPlain = toPlainLatLng(pickupLatLng);
    const dropPlain = toPlainLatLng(dropLatLng);
    const plat = pickupPlain ? pickupPlain.lat : null;
    const plng = pickupPlain ? pickupPlain.lng : null;
    const dlat = dropPlain ? dropPlain.lat : null;
    const dlng = dropPlain ? dropPlain.lng : null;

    if (!pickup) {
        alert('Please set a pickup location.');
        return;
    }
    if (!plat || !plng) {
        alert('Pickup coordinates are missing. Please set pickup using current location or search.');
        return;
    }
    if (currentService !== 'rental') {
        if (!drop) {
            alert('Please enter a destination.');
            return;
        }
        if (!dlat || !dlng) {
            alert('Drop coordinates are missing. Please select a destination from search.');
            return;
        }
        if (!lastCalculatedDistance || lastCalculatedDistance <= 0) {
            alert('Unable to calculate route. Please wait for the route to finish or try again.');
            calculateRoute();
            return;
        }
        const localCategory = activeCategory ? (activeCategory.querySelector('h6')?.innerText || '') : '';
        if (dehradunLocalRate(localCategory) !== null && lastCalculatedDistance > pricingSettings.dehradun_max_local_km) {
            alert('This trip is beyond the ' + pricingSettings.dehradun_max_local_km + ' km Dehradun local limit. Local rates cannot be used for this trip.');
            return;
        }
    }
    if (!catId) {
        alert('Please select a vehicle category.');
        return;
    }
    if (!Number.isFinite(totalFare) || totalFare <= 0 || /unavailable/i.test(document.getElementById('total-fare').innerText || '')) {
        alert('Fare is not ready. Wait for the route, then choose a vehicle.');
        return;
    }

    const confirmBtn = (typeof event !== 'undefined' && event && event.target)
        ? (event.target.closest('button') || event.target)
        : document.querySelector('#ride-options-sheet button.btn-brand');
    const originalHtml = confirmBtn ? confirmBtn.innerHTML : '';
    isSubmittingBooking = true;
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
    }

    let notesText = 'Preference: ' + acPreference.toUpperCase();
    if (typeof stopLatLng !== 'undefined' && stopLatLng) {
        const stopAddress = document.getElementById('stop-location').value;
        notesText += ' | Stop Point: ' + stopAddress;
    }
    if (currentService === 'parcel') {
        notesText += ' | Parcel Note: ' + document.getElementById('parcel-note').value;
    }
    const formData = {
        _token: '{{ csrf_token() }}',
        pickup_location: pickup,
        dropoff_location: drop || pickup,
        pickup_lat: plat,
        pickup_lng: plng,
        dropoff_lat: dlat ?? plat,
        dropoff_lng: dlng ?? plng,
        distance: lastCalculatedDistance || 0,
        service_type: currentService === 'rental' ? 'ride' : currentService,
        vehicle_category_id: catId,
        fare: totalFare,
        offer_extra: Number.isFinite(customerOfferExtra) && customerOfferExtra > 0 ? customerOfferExtra : 0,
        ac_preference: acPreference,
        coupon_code: discount > 0 ? ((document.getElementById('coupon-code') || {}).value || '') : '',
        parcel_weight: currentService === 'parcel' ? (parseFloat((document.getElementById('parcel-weight') || {}).value) || 0) : 0,
        is_rental: currentService === 'rental',
        payment_method: selectedPaymentMethod || 'cash',
        parcel_details: currentService === 'parcel' ? document.getElementById('parcel-note').value : null,
        notes: notesText
    };

    const restoreConfirmBtn = () => {
        isSubmittingBooking = false;
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalHtml;
        }
    };

    fetch("{{ route('rider.bookings.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            const firstError = data.errors ? Object.values(data.errors)[0] : null;
            const msg = (Array.isArray(firstError) ? firstError[0] : null) || data.message || 'Error creating booking';
            throw new Error(msg);
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            currentBookingId = data.booking.id;
            const quoted = document.getElementById('quoted-fare');
            if (quoted) quoted.textContent = formatInrFare(data.booking.fare);
            document.getElementById('ride-options-sheet').classList.add('d-none');
            document.getElementById('bids-sheet').classList.remove('d-none');
            const sheet = document.getElementById('main-sheet');
            if (sheet) {
                sheet.classList.remove('sheet-picking');
                sheet.scrollTop = 0;
            }
            const waiting = document.querySelector('#bids-sheet .opacity-50 p, #bids-sheet p');
            startBidPolling();
        } else {
            alert(data.message || 'Error creating booking');
            restoreConfirmBtn();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Something went wrong. Please try again.');
        restoreConfirmBtn();
    });
}

function showTrackingRoute(booking) {
    const el = document.getElementById('tracking-route');
    if (!el || !booking) return;
    const pickup = booking.pickup_location || '';
    const drop = booking.dropoff_location || '';
    el.textContent = drop ? (pickup + ' → ' + drop) : pickup;
}

function showRideOtp(booking) {
    const box = document.getElementById('ride-otp-box');
    const code = document.getElementById('ride-otp-code');
    const note = document.getElementById('ride-otp-note');
    if (!box || !code) return;
    if (!booking) {
        box.classList.add('d-none');
        return;
    }
    if (booking.status === 'accepted' && booking.ride_otp) {
        box.classList.remove('d-none');
        code.textContent = booking.ride_otp;
        if (note) note.textContent = booking.arrived_at
            ? 'Your partner has reached you. Read this OTP to them so they can start the trip.'
            : 'Keep this OTP until your partner reaches you, then read it to them.';
    } else if (booking.status === 'ongoing') {
        box.classList.remove('d-none');
        code.textContent = 'Started';
        if (note) note.textContent = 'OTP verified. Your trip is in progress.';
    } else if (booking.status === 'completed') {
        box.classList.remove('d-none');
        code.textContent = 'Done';
        if (note) note.textContent = 'This ride is completed.';
    } else {
        box.classList.add('d-none');
    }
}

function startBidPolling() {
    if (bidPollingInterval) clearInterval(bidPollingInterval);
    
    // Poll every 5 seconds
    fetchBids();
    bidPollingInterval = setInterval(fetchBids, 5000);
}

function fetchBids() {
    if (!currentBookingId) return;

    fetch(`/rider/bookings/${currentBookingId}/bids`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const booking = data.booking;
            const quoted = document.getElementById('quoted-fare');
            if (quoted && booking) quoted.textContent = formatInrFare(booking.fare);
            
            // Check if booking has already been accepted/confirmed by a driver (e.g. Instant Accept or accepted bid)
            if (booking && (booking.status === 'accepted' || booking.status === 'arrived' || booking.status === 'ongoing' || booking.status === 'completed')) {
                clearInterval(bidPollingInterval);
                const driver = booking.driver;

                // Update UI to tracking sheet
                document.getElementById('bids-sheet').classList.add('d-none');
                document.getElementById('tracking-sheet').classList.remove('d-none');
                const sheet = document.getElementById('main-sheet');
                if (sheet) sheet.scrollTop = 0;
                
                if (driver) {
                    document.getElementById('driver-name').innerText = driver.name;
                    document.getElementById('driver-info').innerHTML = `<i class="bi bi-star-fill text-brand"></i> 4.9 Driver • ${driver.vehicle_type || 'Vehicle'}`;
                    document.getElementById('vehicle-reg').innerText = driver.vehicle_number || 'N/A';
                    document.getElementById('final-fare').innerText = formatInrFare(booking.fare);
                    document.getElementById('driver-photo').src = driver.profile_image ? (String(driver.profile_image).startsWith('http') ? driver.profile_image : `/storage/${driver.profile_image}`) : `https://i.pravatar.cc/100?u=${driver.id}`;
                    document.getElementById('driver-phone').href = `tel:${driver.mobile}`;
                }

                const title = document.getElementById('tracking-title');
                const desc = document.getElementById('tracking-desc');

                if (booking.status === 'arrived') {
                    title.innerText = 'Driver has arrived!';
                    title.className = 'fw-bold mb-1 text-success';
                    desc.innerText = 'Please meet the driver at the pickup point';
                } else if (booking.status === 'ongoing') {
                    title.innerText = 'Ride started';
                    title.className = 'fw-bold mb-1 text-info';
                    desc.innerText = 'Heading to your destination';
                    const controls = document.querySelector('.ride-controls');
                    if (controls) controls.classList.add('d-none');
                } else {
                    title.innerText = 'Booking Confirmed!';
                    title.className = 'fw-bold mb-1 text-brand';
                    desc.innerText = 'Driver is arriving in 5 mins';
                }

                showRideOtp(booking);
                showTrackingRoute(booking);
                if (booking.service_type === 'parcel') {
                    title.innerText = 'Partner Assigned';
                    const badge = document.getElementById('parcel-badge');
                    if (badge) badge.classList.remove('d-none');
                }

                // Start status polling to listen for further updates
                startStatusPolling();
                
                // Set chat link
                const chatLink = document.getElementById('chat-driver-link');
                if (chatLink) chatLink.href = `/chat/${currentBookingId}`;
                return;
            }

            const bidContainer = document.getElementById('bid-list');
            const bids = data.bids;
            document.getElementById('bid-count').innerText = `${bids.length} Bids`;

            if (bids.length === 0) {
                bidContainer.innerHTML = `
                    <div class="text-center py-5 opacity-50">
                        <div class="spinner-border text-brand spinner-border-sm mb-3" role="status"></div>
                        <p>Waiting for a driver to accept your offer...</p>
                    </div>`;
                return;
            }

            let html = '';
            bids.forEach(bid => {
                const driver = bid.driver;
                html += `
                    <div class="ride-option d-flex align-items-center justify-content-between p-3 mb-3 rounded-4 active bg-white border border-light">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle overflow-hidden border border-light me-3" style="width: 50px; height: 50px;">
                                <img src="${driver.profile_image ? '/uploads/profiles/' + driver.profile_image : 'https://i.pravatar.cc/100?u=' + driver.id}" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">${driver.name}</h6>
                                <p class="mb-0 small text-secondary">
                                    <i class="bi bi-star-fill text-brand"></i> ${parseFloat(driver.rating || 4.9).toFixed(1)} • ${driver.vehicle_type || 'Driver'}
                                </p>
                                ${bid.notes ? `<p class="mb-0 x-small text-brand opacity-75 mt-1">"${bid.notes}"</p>` : ''}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="extra-small text-secondary">Driver offer</div>
                            <h5 class="mb-2 fw-bold text-brand">${formatInrFare(bid.bid_amount)}</h5>
                            <div class="d-flex gap-2">
                                <button onclick="acceptBid(${bid.id})" class="btn btn-brand btn-sm px-3 rounded-pill fw-bold shadow">Accept</button>
                                <button onclick="rejectBid(${bid.id})" class="btn btn-outline-danger btn-sm px-3 rounded-pill">Reject</button>
                            </div>
                        </div>
                    </div>`;
            });
            bidContainer.innerHTML = html;
        }
    });
}

let acceptingBid = false;
function acceptBid(bidId) {
    if (acceptingBid) return;
    if (!confirm('Are you sure you want to accept this offer?')) return;
    acceptingBid = true;

    fetch(`/rider/bookings/bids/${bidId}/accept`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Unable to accept this bid.');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            clearInterval(bidPollingInterval);
            const booking = data.booking;
            const driver = booking.driver;

            // Update UI
            document.getElementById('bids-sheet').classList.add('d-none');
            document.getElementById('tracking-sheet').classList.remove('d-none');
            
            document.getElementById('driver-name').innerText = driver.name;
            document.getElementById('driver-info').innerHTML = `<i class="bi bi-star-fill text-brand"></i> 4.9 Driver • ${driver.vehicle_type || 'Vehicle'}`;
            document.getElementById('vehicle-reg').innerText = driver.vehicle_number || 'N/A';
            document.getElementById('final-fare').innerText = formatInrFare(booking.fare);
            document.getElementById('driver-photo').src = driver.profile_image ? `/uploads/profiles/${driver.profile_image}` : `https://i.pravatar.cc/100?u=${driver.id}`;
            document.getElementById('driver-phone').href = `tel:${driver.mobile}`;

            if (booking.service_type === 'parcel') {
                document.getElementById('tracking-title').innerText = 'Partner Assigned';
                document.getElementById('parcel-badge').classList.remove('d-none');
            }

            showRideOtp(booking);
            // Start status polling
            startStatusPolling();
            
            // Set chat link
            document.getElementById('chat-driver-link').href = `/chat/${currentBookingId}`;
        } else {
            acceptingBid = false;
            alert(data.message || 'Unable to accept this bid.');
        }
    })
    .catch(err => {
        acceptingBid = false;
        alert(err.message || 'Unable to accept this bid.');
    });
}

let statusPollingInterval = null;
let driverMarker = null;
let mapFittedToDriver = false;

function startStatusPolling() {
    if (statusPollingInterval) clearInterval(statusPollingInterval);
    mapFittedToDriver = false;
    statusPollingInterval = setInterval(fetchStatus, 5000);
}

function updateDriverMarker(lat, lng, vehicleType) {
    if (!lat || !lng) return;
    
    let iconUrl = 'https://cdn-icons-png.flaticon.com/512/3198/3198343.png'; // Elegant taxi icon
    const vehicle = (vehicleType || '').toLowerCase();
    if (vehicle.includes('bike') || vehicle.includes('moto') || vehicle.includes('two')) {
        iconUrl = 'https://cdn-icons-png.flaticon.com/512/3198/3198336.png'; // Bike icon
    } else if (vehicle.includes('truck') || vehicle.includes('freight') || vehicle.includes('loader')) {
        iconUrl = 'https://cdn-icons-png.flaticon.com/512/3198/3198341.png'; // Truck icon
    } else if (vehicle.includes('auto') || vehicle.includes('rickshaw')) {
        iconUrl = 'https://cdn-icons-png.flaticon.com/512/2898/2898584.png'; // Auto icon
    }

    if (isGoogleMaps) {
        if (!driverMarker) {
            driverMarker = new google.maps.Marker({
                map: map,
                icon: {
                    url: iconUrl,
                    scaledSize: new google.maps.Size(40, 40),
                    origin: new google.maps.Point(0,0),
                    anchor: new google.maps.Point(20, 20)
                },
                title: 'Driver Location'
            });
        }
        driverMarker.setPosition({ lat: parseFloat(lat), lng: parseFloat(lng) });
    } else {
        // Leaflet marker update
        if (!driverMarker) {
            const customIcon = L.icon({
                iconUrl: iconUrl,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });
            driverMarker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
        } else {
            driverMarker.setLatLng([lat, lng]);
        }
    }
}

function updateDriverETA(driverLat, driverLng, targetLat, targetLng, status) {
    if (!driverLat || !driverLng || !targetLat || !targetLng) return;

    const p1 = driverLng + ',' + driverLat;
    const p2 = targetLng + ',' + targetLat;

    fetch(`https://router.project-osrm.org/route/v1/driving/${p1};${p2}`)
    .then(res => res.json())
    .then(data => {
        if (data.routes && data.routes.length > 0) {
            const route = data.routes[0];
            const distKm = route.distance / 1000;
            const durationMin = Math.round(route.duration / 60) || 1;
            
            const desc = document.getElementById('tracking-desc');
            if (desc) {
                if (status === 'ongoing') {
                    desc.innerHTML = `<i class="bi bi-geo-alt-fill text-brand me-1"></i> Heading to destination • <span class="fw-bold text-dark">${durationMin} mins</span> (${distKm.toFixed(1)} km left)`;
                } else {
                    desc.innerHTML = `<i class="bi bi-clock-history text-brand me-1"></i> Arriving in <span class="fw-bold text-dark">${durationMin} mins</span> (${distKm.toFixed(1)} km away)`;
                }
            }
        } else {
            fallbackETA(driverLat, driverLng, targetLat, targetLng, status);
        }
    })
    .catch(err => {
        console.warn("OSRM ETA fetch failed, falling back to approximation", err);
        fallbackETA(driverLat, driverLng, targetLat, targetLng, status);
    });
}

function fallbackETA(driverLat, driverLng, targetLat, targetLng, status) {
    const R = 6371; // km
    const dLat = (targetLat - driverLat) * Math.PI / 180;
    const dLng = (targetLng - driverLng) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(driverLat * Math.PI / 180) * Math.cos(targetLat * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distKm = R * c * 1.3; // 1.3x road coefficient
    const durationMin = Math.round(distKm * 2.5) || 1; // 2.5 mins per km

    const desc = document.getElementById('tracking-desc');
    if (desc) {
        if (status === 'ongoing') {
            desc.innerHTML = `<i class="bi bi-geo-alt-fill text-brand me-1"></i> Heading to destination • <span class="fw-bold text-dark">${durationMin} mins</span> (${distKm.toFixed(1)} km left)`;
        } else {
            desc.innerHTML = `<i class="bi bi-clock-history text-brand me-1"></i> Arriving in <span class="fw-bold text-dark">${durationMin} mins</span> (${distKm.toFixed(1)} km away)`;
        }
    }
}

function fitMapToDriverAndRider(driverLat, driverLng, pickupLat, pickupLng) {
    if (!map) return;
    if (isGoogleMaps) {
        const bounds = new google.maps.LatLngBounds();
        bounds.extend({ lat: parseFloat(driverLat), lng: parseFloat(driverLng) });
        bounds.extend({ lat: parseFloat(pickupLat), lng: parseFloat(pickupLng) });
        map.fitBounds(bounds);
    } else {
        const bounds = L.latLngBounds([
            [parseFloat(driverLat), parseFloat(driverLng)],
            [parseFloat(pickupLat), parseFloat(pickupLng)]
        ]);
        map.fitBounds(bounds, { padding: [50, 50] });
    }
}

function fetchStatus() {
    if (!currentBookingId) return;

    fetch(`/rider/bookings/${currentBookingId}/status`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const booking = data.booking;
            const status = booking.status;
            
            const title = document.getElementById('tracking-title');
            const desc = document.getElementById('tracking-desc');
            showTrackingRoute(booking);

            // Render/Update driver's real-time live location on the map!
            if (booking.driver && booking.driver.current_lat && booking.driver.current_lng) {
                const dLat = parseFloat(booking.driver.current_lat);
                const dLng = parseFloat(booking.driver.current_lng);
                
                updateDriverMarker(dLat, dLng, booking.driver.vehicle_type);
                
                // Determine target coordinates (Pickup for pre-trip, Dropoff during trip)
                let targetLat = parseFloat(booking.pickup_lat);
                let targetLng = parseFloat(booking.pickup_lng);
                if (status === 'ongoing') {
                    targetLat = parseFloat(booking.dropoff_lat);
                    targetLng = parseFloat(booking.dropoff_lng);
                }

                // Update real-time OSRM/Math ETA text display
                if (status !== 'completed' && status !== 'cancelled' && status !== 'arrived') {
                    updateDriverETA(dLat, dLng, targetLat, targetLng, status);
                }

                // Fit map bounds on initial load to show both points perfectly
                if (!mapFittedToDriver) {
                    fitMapToDriverAndRider(dLat, dLng, targetLat, targetLng);
                    mapFittedToDriver = true;
                }
            }

            if (status === 'accepted') {
                title.innerText = booking.arrived_at ? 'Partner has reached you' : 'Driver is on the way';
                desc.innerText = booking.arrived_at
                    ? 'Share the OTP below so your partner can start the trip'
                    : (data.eta_minutes ? ('Arriving in about ' + data.eta_minutes + ' min') : 'Please wait at the pickup point');
                title.className = 'fw-bold mb-1 text-brand';
            } else if (status === 'arrived') {
                // Legacy label — backend uses accepted → ongoing (no separate arrived status)
                title.innerText = 'Driver has arrived!';
                desc.innerText = 'Please meet the driver at the pickup point';
                title.className = 'fw-bold mb-1 text-success';
            } else if (status === 'ongoing') {
                title.innerText = 'Ride in progress';
                title.className = 'fw-bold mb-1 text-info';
                desc.innerText = 'Heading to your destination';
                const controls = document.querySelector('.ride-controls');
                if (controls) controls.classList.add('d-none');
            } else if (status === 'completed') {
                clearInterval(statusPollingInterval);
                title.innerText = 'Ride completed!';
                title.className = 'fw-bold mb-1 text-success';
                desc.innerText = 'Hope you had a great journey!';
                
                // Remove driver marker on completion
                if (driverMarker) {
                    if (isGoogleMaps) driverMarker.setMap(null);
                    else map.removeLayer(driverMarker);
                    driverMarker = null;
                }
                
                // Show rating modal
                openRatingModal();
            } else if (status === 'cancelled') {
                clearInterval(statusPollingInterval);
                alert('This ride has been cancelled.');
                window.location.reload();
            }

            showRideOtp(booking);
        }
    });
}

function openRatingModal() { document.getElementById('rating-modal').classList.remove('d-none'); }
function closeRatingModal() { document.getElementById('rating-modal').classList.add('d-none'); }

function setRating(val) {
    document.getElementById('submit-rating-value').value = val;
    document.querySelectorAll('.star-icon').forEach((star, index) => {
        if (index < val) {
            star.classList.replace('text-light', 'text-brand');
        } else {
            star.classList.replace('text-brand', 'text-light');
        }
    });
}

function submitReview() {
    const val = document.getElementById('submit-rating-value').value;
    const comment = document.getElementById('submit-rating-comment').value;

    if (val == 0) return alert('Please select a star rating.');

    const btn = document.getElementById('post-review-btn');
    btn.disabled = true;
    btn.innerHTML = 'Submitting...';

    fetch('/rider/ratings', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({ booking_id: currentBookingId, rating: val, comment: comment })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rating submitted! Redirecting to your ride details.');
            window.location.href = `/rider/bookings/${currentBookingId}/show`;
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = 'Submit Feedback';
        }
    });
}
function openCancelModal() {
    const modal = document.getElementById('cancel-modal');
    if (!modal || !currentBookingId) return;
    modal.querySelectorAll('input[name="cancel_reason"]').forEach(input => { input.checked = false; });
    const error = document.getElementById('cancel-reason-error');
    if (error) error.classList.add('d-none');
    const btn = document.getElementById('cancel-confirm-btn');
    if (btn) {
        btn.disabled = false;
        btn.textContent = 'Cancel ride';
    }
    modal.classList.remove('d-none');
    modal.setAttribute('aria-hidden', 'false');
}

function openDriverChat() {
    const link = document.getElementById('chat-driver-link');
    const href = link ? link.getAttribute('href') : '';
    if (!currentBookingId || !href || href === '#') {
        alert('Chat is available after a partner accepts the ride.');
        return false;
    }
    window.location.href = href;
    return false;
}
function closeCancelModal() {
    const modal = document.getElementById('cancel-modal');
    if (!modal) return;
    modal.classList.add('d-none');
    modal.setAttribute('aria-hidden', 'true');
}

function submitCancellation() {
    if (!currentBookingId) return;
    const selected = document.querySelector('#cancel-modal input[name="cancel_reason"]:checked');
    const error = document.getElementById('cancel-reason-error');
    if (!selected) {
        if (error) error.classList.remove('d-none');
        return;
    }
    if (error) error.classList.add('d-none');
    const btn = document.getElementById('cancel-confirm-btn');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Cancelling...';
    }

    fetch(`/rider/bookings/${currentBookingId}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ reason: selected.value })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Please select a reason to cancel this ride.');
        }
        if (typeof bidPollingInterval !== 'undefined' && bidPollingInterval) clearInterval(bidPollingInterval);
        window.location.reload();
    })
    .catch(err => {
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Cancel ride';
        }
        alert(err.message || 'Unable to cancel this ride.');
    });
}

const rejectingBids = new Set();
function rejectBid(bidId) {
    const id = String(bidId);
    if (rejectingBids.has(id)) return;
    rejectingBids.add(id);
    fetch(`/rider/bookings/bids/${bidId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Unable to reject this bid.');
        }
        fetchBids();
    })
    .catch(err => alert(err.message || 'Unable to reject this bid.'))
    .finally(() => rejectingBids.delete(id));
}

function cancelBookingAction() {
    openCancelModal();
}

function initializeDropTimes() {
    document.querySelectorAll('.ride-option').forEach(option => {
        const dropDisplay = option.querySelector('.drop-time-display');
        if (dropDisplay) {
            const waitMin = parseInt(dropDisplay.dataset.wait) || 5;
            const totalMin = waitMin + 15; // default 15 mins travel time if no route calculated
            
            const dropDate = new Date();
            dropDate.setMinutes(dropDate.getMinutes() + totalMin);
            
            let hours = dropDate.getHours();
            const minutes = dropDate.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12;
            dropDisplay.innerText = `${hours}:${minutes} ${ampm}`;
        }
    });
}

window.addEventListener('load', function() {
    const splash = document.getElementById('splash');
    if(splash) {
        splash.style.opacity = '0';
        setTimeout(() => { if(splash.parentNode) splash.remove(); }, 500);
    }
    setService('ride'); // Default
    initializeDropTimes();
    
    @if(($sys_settings['map_provider'] ?? 'google') != 'google' || empty($sys_settings['google_maps_key']))
        // If not using Google Maps or no API key, init Leaflet directly
        initMap();
    @else
        const bootGoogleMap = () => {
            if (mapInitialized) return;
            if (typeof google !== 'undefined' && google.maps && typeof initMap === 'function') {
                initMap();
            }
        };
        bootGoogleMap();
        setTimeout(bootGoogleMap, 600);
        setTimeout(() => {
            if (mapInitialized) return;
            if (typeof google === 'undefined') {
                const placeholder = document.getElementById('map-placeholder');
                if (placeholder) placeholder.innerHTML = '<div class="text-dark text-center p-4">Loading map is taking longer than usual...<br><button onclick="location.reload()" class="btn btn-brand btn-sm mt-2">Retry</button></div>';
            } else {
                bootGoogleMap();
            }
        }, 5000);
    @endif

    // Check if resuming from activity page
    const urlParams = new URLSearchParams(window.location.search);
    const resumeBookingId = urlParams.get('booking_id');
    if (resumeBookingId) {
        currentBookingId = resumeBookingId;
        document.getElementById('location-sheet').classList.add('d-none');
        document.getElementById('bids-sheet').classList.remove('d-none');
        startBidPolling();
    }
});

// Extra Safety fallback: Force remove splash after 3 seconds anyway
setTimeout(() => {
    const splash = document.getElementById('splash');
    if(splash) {
        splash.style.opacity = '0';
        setTimeout(() => { if(splash.parentNode) splash.remove(); }, 500);
    }
}, 3000);
</script>
<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.bi-spin { animation: spin 1s linear infinite; color: var(--primary-color) !important; }
.active-scale:active { transform: scale(0.98); }
.cursor-pointer { cursor: pointer; }
.font-xs { font-size: 0.65rem; }
.x-small { font-size: 0.75rem; }
.ls-1 { letter-spacing: 0.5px; }
.ac-selection-panel .btn {
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}
.ac-selection-panel .btn-brand {
    box-shadow: 0 4px 15px rgba(var(--primary-rgb), 0.3);
}
.text-secondary {
    color: #555555 !important;
}
.text-dark {
    color: #111111 !important;
}
::placeholder {
    color: #777777 !important;
    opacity: 1;
}
.hover-bg-light:hover { background-color: #f8f9fa; }
.free-autocomplete-results div { transition: background 0.2s; }
</style>

@endsection











