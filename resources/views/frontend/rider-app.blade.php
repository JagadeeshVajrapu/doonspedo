@extends('layouts.app')

@section('title', 'Doonspedo - Book a Ride')
@section('body_class', 'bg-light text-dark overflow-hidden')
@section('needs_maps')1@endsection

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
        <img src="{{ asset($sys_settings['app_logo']) }}" alt="{{ $sys_settings['app_name'] ?? 'Doonspedo' }}" class="img-fluid mb-4" style="max-height: 80px;">
    @else
        <h1 class="text-brand fw-bold display-3 mb-3">DOONS<span class="text-dark">PEDO</span></h1>
    @endif
    <div class="spinner-border text-brand" role="status" aria-label="Loading"></div>
</div>

<div class="app-container d-flex flex-column" style="height: 100vh;">
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
    <main class="flex-grow-1 position-relative bg-secondary bg-opacity-10 overflow-hidden" id="main-content" aria-label="Map and booking">
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
                <p class="small text-secondary mb-3 px-2 text-truncate" id="map-picker-address" style="max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Detecting location...</p>
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
    <section id="main-sheet" class="rider-booking-sheet bg-white border-top border-light rounded-top-5 p-3 shadow-lg custom-scrollbar" style="margin-top: -40px; z-index: 100; position: relative; min-height: auto; max-height: 85vh; overflow-y: auto;">
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
                            <input type="text" id="pickup-location" class="form-control bg-white border-0 text-dark py-3" style="outline: none; box-shadow: none;" placeholder="Pickup point" value="My Current Location" aria-labelledby="pickup-label" autocomplete="street-address">
                            <button id="detect-btn" onclick="detectLocation()" class="btn btn-link text-secondary border-0 bg-white" type="button" aria-label="Detect location"><i class="bi bi-crosshair" aria-hidden="true"></i></button>
                        </div>
                    </div>

                    <!-- Pickup to Stop Line (Initially hidden) -->
                    <div id="pickup-to-stop-line" class="ms-4 my-1 border-start border-light d-none" style="height: 20px; width: 0; border-style: dashed !important;"></div>

                    <!-- Dynamic Stop Input (Initially hidden) -->
                    <div id="stop-location-container" class="position-relative mb-2 d-none">
                        <div class="input-group rider-location-field border p-1 shadow-sm m-0">
                            <span class="input-group-text bg-white border-0 text-warning">
                                <i class="bi bi-geo-alt-fill"></i>
                            </span>
                            <input type="text" id="stop-location" class="form-control bg-white border-0 text-dark py-3" style="outline: none; box-shadow: none;" placeholder="Stop point (optional)" aria-label="Stop location">
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
                            <input type="text" id="drop-location" class="form-control bg-white border-0 text-dark py-3" style="outline: none; box-shadow: none;" placeholder="Enter Destination" aria-labelledby="drop-label" autocomplete="street-address">
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

                <div class="ride-options mb-2 custom-scrollbar" style="max-height: 160px; overflow-y: auto;" id="ride-option-list">
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
                            <h6 class="mb-0 fw-bold text-dark fare-display-item" id="fare-cat-{{ $category->id }}" style="font-size: 1.1rem;">₹{{ number_format($category->base_fare, 0) }}</h6>
                            @if($index == 0)
                                <span class="text-success fw-bold" style="font-size: 0.65rem;">BEST PRICE</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>


                <!-- Price Breakdown Section (New) -->
                <div id="price-breakdown" class="mb-3 p-3 bg-light rounded-4 d-none" style="font-size: 0.85rem;">
                    <div class="d-flex justify-content-between mb-1 d-none">
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

                <div class="booking-action-section">
                    <button onclick="confirmBooking()" class="btn btn-brand w-100 py-3 fw-bold fs-5 rounded-4 shadow-lg active-scale border-0 d-flex justify-content-between align-items-center px-4">
                        <div class="text-start">
                            <p class="mb-0 x-small opacity-75 fw-normal">Total Price</p>
                            <span>Book <span id="selected-ride-name">Ride</span></span>
                        </div>
                        <span id="total-fare" class="fs-4">Rs. 0.00</span>
                    </button>
                </div>
            </div>

            <!-- 4. Bid List & Selection (New) -->
            <div id="bids-sheet" class="d-none">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Driver Bids</h5>
                    <span class="badge bg-brand text-dark rounded-pill py-1 px-3" id="bid-count">0 Bids</span>
                </div>
                
                <div id="bid-list" class="mb-4 custom-scrollbar" style="max-height: 350px; overflow-y: auto;">
                    <!-- Bids will be injected here -->
                    <div class="text-center py-5 opacity-50">
                        <div class="spinner-border text-brand spinner-border-sm mb-3" role="status"></div>
                        <p>Waiting for drivers to bid...</p>
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
                            <p class="mb-0 small text-secondary">Fare: <span id="final-fare" class="text-dark fw-bold">?0</span></p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <a id="driver-phone" href="tel:+910000000000" class="btn btn-white shadow-sm flex-grow-1 rounded-pill py-2 small fw-bold border-light">
                            <i class="bi bi-telephone-fill me-2"></i> Call
                        </a>
                        <button class="btn btn-white shadow-sm flex-grow-1 rounded-pill py-2 small fw-bold border-light">
                            <i class="bi bi-chat-dots-fill me-2"></i> Message
                        </button>
                    </div>
                </div>

                <div class="ride-controls d-flex gap-2">
                    <button onclick="openCancelModal()" class="btn btn-outline-danger flex-grow-1 py-3 rounded-4 border-0 bg-danger bg-opacity-10 fw-bold shadow-sm">Cancel Ride</button>
                    <a id="chat-driver-link" href="#" class="btn btn-light py-3 rounded-4 border-light shadow-sm" style="width: 60px;" aria-label="Chat with driver"><i class="bi bi-chat-dots-fill text-brand" aria-hidden="true"></i></a>
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
                <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-info-circle-fill text-brand me-1"></i> How to Allow Location (?????? ???? ???? ????)</h6>
                <ol class="small text-secondary ps-3 mb-0" style="line-height: 1.6;">
                    <li>URL ??? ??? ??? ???? ?? (Lock) ?? Settings ?? ???? ?? ????? ?????</li>
                    <li>Permissions ??? ???? <b>Location</b> ?? <b>Allow (????)</b> ?? ????</li>
                    <li>??? ?? ????? (Refresh) ?????</li>
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
        <div class="d-flex justify-content-center gap-3 pt-2 border-top w-100 mt-1" style="font-size: 11px;">
            <a href="/policy/privacy" class="text-secondary text-decoration-none fw-semibold">Privacy</a>
            <a href="/policy/data-deletion" class="text-secondary text-decoration-none fw-semibold">Data Deletion</a>
            <a href="/policy/refund" class="text-secondary text-decoration-none fw-semibold">Refunds</a>
        </div>
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
    z-index: 3000 !important;
}
</style>

<script>
let currentService = 'ride';
let baseFare = 0;
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
    non_ac_rate_per_km: parseFloat("{{ $sys_settings['non_ac_rate_per_km'] ?? '10.0' }}")
};
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
const mapProvider = "{{ $sys_settings['map_provider'] ?? 'google' }}";


// Make initMap global for Google Maps callback
window.initMap = function() {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        setTimeout(window.initMap, 100);
        return;
    }

    const defaultLoc = { lat: 30.3165, lng: 78.0322 }; // Dehradun
    
    if (mapProvider === 'google' && typeof google !== 'undefined') {
        try {
            isGoogleMaps = true;
            map = new google.maps.Map(mapElement, {
                center: defaultLoc,
                zoom: 15,
                disableDefaultUI: true,
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
            pickupLatLng = { lat: defaultLoc.lat, lng: defaultLoc.lng }; // Set immediate fallback coordinates
            initAutocomplete();
            detectLocation();

            // Google Maps Drag Event to Select Exact Location
            map.addListener('dragstart', () => {
                document.getElementById('center-pin').classList.add('map-moving');
            });
            
            map.addListener('idle', () => {
                document.getElementById('center-pin').classList.remove('map-moving');
                
                const center = map.getCenter();
                const currentLatLng = { lat: center.lat(), lng: center.lng() };
                
                if (typeof activeMapPickerType !== 'undefined' && activeMapPickerType) {
                    reverseGeocode(currentLatLng.lat, currentLatLng.lng, 'map-picker-address');
                    return;
                }
                
                if (document.getElementById('location-sheet').classList.contains('d-none')) return;
                
                pickupLatLng = currentLatLng;
                
                const pickupInput = document.getElementById('pickup-location');
                pickupInput.value = "?? Finding place...";
                
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ location: pickupLatLng }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        pickupInput.value = results[0].formatted_address;
                        updateMarker('pickup', pickupLatLng);
                        if (dropLatLng) calculateRoute();
                    } else {
                        pickupInput.value = `${center.lat().toFixed(5)}, ${center.lng().toFixed(5)}`;
                    }
                });
            });

        } catch (e) {
            console.error("Google Maps init failed, switching to Leaflet", e);
            initLeaflet(defaultLoc);
        }
    } else {
        initLeaflet(defaultLoc);
    }
};


function initLeaflet(loc) {
    isGoogleMaps = false;
    pickupLatLng = [loc.lat, loc.lng]; // Set immediate fallback coordinates
    const mapElement = document.getElementById('map');
    
    // Clear the element
    mapElement.innerHTML = '';
    
    map = L.map(mapElement, {
        zoomControl: false,
        attributionControl: false
    }).setView([loc.lat, loc.lng], 15);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19
    }).addTo(map);

    document.getElementById('map-placeholder').style.display = 'none';
    document.getElementById('center-pin').classList.remove('d-none');
    
    // Setup Free Autocomplete (Photon)
    setupFreeAutocomplete('pickup-location');
    setupFreeAutocomplete('drop-location');
    setupFreeAutocomplete('stop-location');
    
    // Map Move Logic for Exact Location
    let isMoving = false;
    map.on('movestart', () => {
        isMoving = true;
        document.getElementById('center-pin').classList.add('map-moving');
    });

    map.on('moveend', () => {
        isMoving = false;
        document.getElementById('center-pin').classList.remove('map-moving');
        
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

    setElementText(input, "?? Finding exact house...");
    
    const requestId = Date.now();
    input.dataset.lastRequest = requestId;

    // Use Nominatim first
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&email=support@cabbooking.com&email=support@cabbooking.com&lat=${lat}&lon=${lng}&zoom=19&addressdetails=1`, {
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

function setupFreeAutocomplete(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const resultsContainer = document.createElement('div');
    resultsContainer.className = 'free-autocomplete-results bg-white shadow-lg rounded-4 position-absolute w-100 overflow-hidden d-none';
    resultsContainer.style.zIndex = '3000';
    resultsContainer.style.top = '100%';
    resultsContainer.style.left = '0';
    resultsContainer.style.maxHeight = '250px';
    resultsContainer.style.overflowY = 'auto';
    
    const wrapper = input.closest('.position-relative');
    if (wrapper) {
        wrapper.appendChild(resultsContainer);
    } else {
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(resultsContainer);
    }

    let debounceTimer;
    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value;
        if (query.length < 3) {
            resultsContainer.classList.add('d-none');
            return;
        }

        // Enforce strict local bias to Dehradun/Uttarakhand region (Doon SPEDO)
        let bias = '';
        let lat = 30.3165; // Default Dehradun Latitude
        let lon = 78.0322; // Default Dehradun Longitude
        
        if (pickupLatLng) {
            try {
                if (Array.isArray(pickupLatLng)) {
                    lat = pickupLatLng[0];
                    lon = pickupLatLng[1];
                } else if (pickupLatLng && typeof pickupLatLng === 'object') {
                    lat = typeof pickupLatLng.lat === 'function' ? pickupLatLng.lat() : pickupLatLng.lat;
                    lon = typeof pickupLatLng.lng === 'function' ? pickupLatLng.lng() : pickupLatLng.lng;
                }
            } catch (e) {
                console.warn("Error getting pickup coords for search bias:", e);
            }
        }
        
        if (lat !== null && lon !== null && !isNaN(lat) && !isNaN(lon)) {
            const offset = 1.2; // 1.2 degrees covers Dehradun and surrounding Uttarakhand areas perfectly
            bias = `&viewbox=${lon-offset},${lat+offset},${lon+offset},${lat-offset}`;
        }

        debounceTimer = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&email=support@cabbooking.com&email=support@cabbooking.com&q=${encodeURIComponent(query)}&countrycodes=in&limit=10${bias}`)
                .then(res => res.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const name = item.display_name;
                            const parts = name.split(',');
                            const primaryName = parts[0].trim();
                            const secondaryName = parts.slice(1, 4).join(',').trim();
                            
                            const div = document.createElement('div');
                            div.className = 'p-3 border-bottom border-light cursor-pointer hover-bg-light';
                            div.innerHTML = `
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-geo-alt-fill text-brand" style="font-size: 1.1rem;"></i>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.9rem; color: #333;">${primaryName}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">${secondaryName}</div>
                                    </div>
                                </div>
                            `;
                            div.onclick = () => {
                                input.value = name;
                                resultsContainer.classList.add('d-none');
                                const latlng = [parseFloat(item.lat), parseFloat(item.lon)];
                                if (inputId === 'pickup-location') pickupLatLng = latlng;
                                else if (inputId === 'stop-location') stopLatLng = latlng;
                                else dropLatLng = latlng;
                                updateMarker(inputId.replace('-location', ''), latlng);
                                calculateRoute();
                            };
                            resultsContainer.appendChild(div);
                        });
                        resultsContainer.classList.remove('d-none');
                    } else {
                        resultsContainer.classList.add('d-none');
                    }
                });
        }, 500);

    });


    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.classList.add('d-none');
        }
    });
}


// Handle API key errors
window.gm_authFailure = function() {
    console.warn('Google Maps Auth Failure. Switching to Leaflet...');
    initLeaflet({ lat: 30.3165, lng: 78.0322 });
    
    // Clear "Oops" from inputs
    ['pickup-location', 'drop-location'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.disabled = false;
            el.value = "";
            el.placeholder = "Enter address manually";
        }
    });
};

function initAutocomplete() {
    try {
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            pickupAutocomplete = new google.maps.places.Autocomplete(document.getElementById('pickup-location'));
            dropAutocomplete = new google.maps.places.Autocomplete(document.getElementById('drop-location'));

            pickupAutocomplete.addListener('place_changed', onPlaceChanged);
            dropAutocomplete.addListener('place_changed', onPlaceChanged);
        }
    } catch (e) {
        console.error("Autocomplete init failed, using manual input", e);
    }
}

function onPlaceChanged() {
    if (!isGoogleMaps) return;
    const pickupPlace = pickupAutocomplete.getPlace();
    const dropPlace = dropAutocomplete.getPlace();

    if (pickupPlace && pickupPlace.geometry) {
        pickupLatLng = pickupPlace.geometry.location;
        updateMarker('pickup', pickupLatLng);
    }

    if (dropPlace && dropPlace.geometry) {
        dropLatLng = dropPlace.geometry.location;
        updateMarker('drop', dropLatLng);
        calculateRoute();
    }
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

function calculateRoute() {
    if (!pickupLatLng && map) {
        const center = map.getCenter();
        pickupLatLng = isGoogleMaps ? center : [center.lat, center.lng];
    }
    if (!dropLatLng) return;
    if (!pickupLatLng || !dropLatLng) return;

    if (isGoogleMaps) {
        const waypoints = [];
        if (stopLatLng) {
            waypoints.push({ location: stopLatLng, stopover: true });
        }
        directionsService.route({
            origin: pickupLatLng,
            destination: dropLatLng,
            waypoints: waypoints,
            travelMode: google.maps.TravelMode.DRIVING
        }, (response, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
                let totalDist = 0;
                response.routes[0].legs.forEach(leg => {
                    totalDist += leg.distance.value / 1000;
                });
                lastCalculatedDistance = totalDist;
                updateFareByDistance(lastCalculatedDistance);
            }
        });
    } else {
        // --- LEAFLET / OSRM LOGIC ---
        
        // 1. Immediate Straight-Line Calculation (for instant feedback)
        const p1ll = L.latLng(pickupLatLng[0], pickupLatLng[1]);
        const p2ll = L.latLng(dropLatLng[0], dropLatLng[1]);
        let immediateDist = 0;
        
        if (stopLatLng) {
            const stopll = L.latLng(stopLatLng[0], stopLatLng[1]);
            immediateDist = ((p1ll.distanceTo(stopll) + stopll.distanceTo(p2ll)) / 1000) * 1.3;
        } else {
            immediateDist = (p1ll.distanceTo(p2ll) / 1000) * 1.3;
        }
        lastCalculatedDistance = immediateDist;
        updateFareByDistance(immediateDist);

        // 2. Road-based Routing using OSRM (Accurate background update)
        const p1 = pickupLatLng[1] + ',' + pickupLatLng[0];
        const p2 = dropLatLng[1] + ',' + dropLatLng[0];
        
        let routeUrl = `https://router.project-osrm.org/route/v1/driving/${p1};${p2}?overview=full&geometries=geojson`;
        if (stopLatLng) {
            const pStop = stopLatLng[1] + ',' + stopLatLng[0];
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
                if (data.routes && data.routes.length > 0) {
                    const route = data.routes[0];
                    const distKm = route.distance / 1000;
                    lastCalculatedDistance = distKm;
                    updateFareByDistance(distKm);

                    // Draw the actual road path and FIT map
                    const coordinates = route.geometry.coordinates.map(c => [c[1], c[0]]);
                    if (window.routeLine) map.removeLayer(window.routeLine);
                    window.routeLine = L.polyline(coordinates, {color: '#cddc29', weight: 6, opacity: 0.9}).addTo(map);
                    
                    map.fitBounds(window.routeLine.getBounds(), {padding: [70, 70]});
                }
            })
            .catch(err => {
                console.warn("OSRM error or timeout, sticking with immediate distance.", err);
                if (window.routeLine) map.removeLayer(window.routeLine);
                const pathPoints = stopLatLng ? [pickupLatLng, stopLatLng, dropLatLng] : [pickupLatLng, dropLatLng];
                window.routeLine = L.polyline(pathPoints, {color: '#cddc29', weight: 5, dashArray: '10, 10'}).addTo(map);
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
        distDisplay.innerText = 'Total Distance: ' + km.toFixed(1) + ' km';
    }

    document.querySelectorAll('.ride-option').forEach(option => {

        const base = parseFloat(option.dataset.base);
        let rate = parseFloat(option.dataset.rate);
        
        const catName = option.querySelector('h6').innerText.toLowerCase();
        const isBikeOrAuto = catName.includes('bike') || catName.includes('auto') || catName.includes('cycle') || catName.includes('moto');
        
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
            fareDisplay.innerText = 'Rs. ' + catFare;
        }

        // Recalculate drop time dynamically
        const dropDisplay = option.querySelector('.drop-time-display');
        if (dropDisplay) {
            const waitMin = parseInt(dropDisplay.dataset.wait) || 5;
            const travelMin = Math.round(km * 2) || 10; // average 30km/h
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
    document.querySelectorAll('.ride-option').forEach(o => o.classList.remove('active'));
    element.classList.add('active');
    
    // Get the RAW base fare from data attribute
    baseFare = parseFloat(element.dataset.base) || 0;
    
    // Update selected ride name in button
    const rideName = element.querySelector('h6').firstChild.textContent.trim();
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

function updateRideOptionsVisibility() {
    document.querySelectorAll('.ride-option').forEach(opt => {
        let hide = false;
        
        // 1. Freight check
        if (currentService === 'ride' || currentService === 'rental') {
            if (opt.classList.contains('freight-cat')) hide = true;
        }
        
        // 2. AC / Non-AC check (Only for ride and rental)
        if (!hide && (currentService === 'ride' || currentService === 'rental')) {
            const catName = opt.querySelector('h6').innerText.toLowerCase();
            const isBikeOrAuto = catName.includes('bike') || catName.includes('auto') || catName.includes('cycle') || catName.includes('moto');
            
            if (acPreference === 'ac' && isBikeOrAuto) {
                hide = true;
            } else if (acPreference === 'non-ac' && !isBikeOrAuto) {
                hide = true;
            }
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
    const activeOption = document.querySelector('.ride-option.active');
    if (!activeOption || activeOption.classList.contains('d-none')) {
        const firstVisible = Array.from(document.querySelectorAll('.ride-option')).find(opt => !opt.classList.contains('d-none'));
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
    
    // Toggle Category Visibility
    document.querySelectorAll('.ride-option').forEach(opt => {
        if (type === 'ride' || type === 'rental') {
            opt.classList.toggle('d-none', opt.classList.contains('freight-cat'));
        } else if (type === 'parcel') {
            opt.classList.toggle('d-none', false);
        }
    });

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
    const activeOption = document.querySelector('.ride-option.active');
    let ratePerKm = 0;
    if (activeOption) {
        let rate = parseFloat(activeOption.dataset.rate);
        const catName = activeOption.querySelector('h6').innerText.toLowerCase();
        const isBikeOrAuto = catName.includes('bike') || catName.includes('auto') || catName.includes('cycle') || catName.includes('moto');
        
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

    document.getElementById('total-fare').innerText = `Rs. ${total.toFixed(2)}`;
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
        titleEl.innerText = "Location is ACTIVE (?????? ???? ??)";
        titleEl.className = "fw-bold mb-1 text-success";
        descEl.innerText = "Your browser is successfully sharing location coordinates.";
        guideEl.classList.add('d-none');
        allowBtn.innerHTML = `<i class="bi bi-check2-all me-1"></i> Location Enabled`;
        allowBtn.disabled = true;
    } else if (state === 'denied') {
        iconContainer.innerHTML = `<div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; animation: pulse-danger 2s infinite;"><i class="bi bi-shield-slash-fill fs-1"></i></div>`;
        titleEl.innerText = "Location BLOCKED (?????? ??? ?? ??)";
        titleEl.className = "fw-bold mb-1 text-danger";
        descEl.innerText = "Please allow location access to automatically detect your house!";
        guideEl.classList.remove('d-none');
        allowBtn.innerHTML = `<i class="bi bi-arrow-clockwise me-1"></i> Try Re-detecting`;
        allowBtn.disabled = false;
    } else {
        iconContainer.innerHTML = `<div class="bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; animation: pulse-warning 2s infinite;"><i class="bi bi-geo-alt-fill fs-1 text-white"></i></div>`;
        titleEl.innerText = "Location Request (?????? ?????? ???)";
        titleEl.className = "fw-bold mb-1 text-warning";
        descEl.innerText = "We need location access to find your exact house on map.";
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
        title.innerText = "Select Pickup Location (????? ?????? ?????)";
        if (pinLabel) pinLabel.innerText = "Pickup Point";
        if (pinIcon) {
            pinIcon.className = "bi bi-geo-alt-fill text-brand";
            pinIcon.style.color = "";
        }
    } else if (type === 'stop') {
        badge.innerText = "SET STOP";
        badge.style.setProperty('background-color', '#ffc107', 'important');
        badge.style.setProperty('color', '#000', 'important');
        title.innerText = "Select Stop Location (????? ?????? ?????)";
        if (pinLabel) pinLabel.innerText = "Stop Point";
        if (pinIcon) {
            pinIcon.className = "bi bi-geo-alt-fill text-warning";
            pinIcon.style.setProperty('color', '#ffc107', 'important');
        }
    } else {
        badge.innerText = "SET DROP";
        badge.style.setProperty('background-color', '#dc3545', 'important');
        badge.style.setProperty('color', '#fff', 'important');
        title.innerText = "Select Drop Location (????? ?????? ?????)";
        if (pinLabel) pinLabel.innerText = "Drop Point";
        if (pinIcon) {
            pinIcon.className = "bi bi-geo-alt-fill text-danger";
            pinIcon.style.setProperty('color', '#dc3545', 'important');
        }
    }

    // Instantly reverse-geocode the current map center
    const center = map.getCenter();
    reverseGeocode(center.lat, center.lng, 'map-picker-address');
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
    const center = map.getCenter();
    const address = document.getElementById('map-picker-address').innerText;
    
    if (activeMapPickerType === 'pickup') {
        pickupLatLng = [center.lat, center.lng];
        document.getElementById('pickup-location').value = address;
        updateMarker('pickup', [center.lat, center.lng]);
    } else if (activeMapPickerType === 'stop') {
        stopLatLng = [center.lat, center.lng];
        document.getElementById('stop-location').value = address;
        updateMarker('stop', [center.lat, center.lng]);
        calculateRoute();
    } else if (activeMapPickerType === 'drop') {
        dropLatLng = [center.lat, center.lng];
        document.getElementById('drop-location').value = address;
        updateMarker('drop', [center.lat, center.lng]);
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
    if (!navigator.geolocation) return;
    pickupInput.value = "Finding exact house...";
    pickupInput.dataset.lastRequest = Date.now();
    detectIcon.classList.add('bi-spin');

    const successCallback = async (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        const latlng = isGoogleMaps ? { lat, lng: lon } : [lat, lon];

        pickupInput.dataset.lastRequest = Date.now();

        if (isGoogleMaps) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latlng }, (results, status) => {
                if (status === "OK" && results[0]) {
                    pickupInput.value = results[0].formatted_address;
                    pickupLatLng = latlng;
                    updateMarker('pickup', latlng);
                }
                detectIcon.classList.remove('bi-spin');
            });
        } else {
            pickupLatLng = [lat, lon];
            map.setView([lat, lon], 18);
            reverseGeocode(lat, lon, 'pickup-location');
            if (dropLatLng) calculateRoute();
            detectIcon.classList.remove('bi-spin');
        }
    };

    const errorCallback = (error) => {
        detectIcon.classList.remove('bi-spin');
        pickupInput.placeholder = "Enter pickup point";
        pickupInput.dataset.lastRequest = Date.now();
        
        console.warn("Geolocation failed/blocked. Applying automatic fallback to Dehradun center...", error);
        
        const defaultLat = 30.3165;
        const defaultLng = 78.0322;
        const defaultLatLng = isGoogleMaps ? { lat: defaultLat, lng: defaultLng } : [defaultLat, defaultLng];
        
        pickupLatLng = defaultLatLng;
        updateMarker('pickup', defaultLatLng);
        
        if (isGoogleMaps) {
            map.setCenter(defaultLatLng);
            map.setZoom(15);
            pickupInput.value = "Dehradun, Uttarakhand";
        } else {
            map.setView([defaultLat, defaultLng], 15);
            reverseGeocode(defaultLat, defaultLng, 'pickup-location');
        }
        
        if (dropLatLng) calculateRoute();
        
        // Auto-close permission modal if open
        closeLocationModal();
    };

    // 1. Try with high-accuracy GPS first (timeout at 6 seconds to avoid hanging indoors)
    navigator.geolocation.getCurrentPosition(
        successCallback,
        (error) => {
            console.warn("GPS high-accuracy failed (indoor signal lock issue). Retrying with low-accuracy cell tower/Wi-Fi positioning...", error);
            // 2. Instantly fallback to low-accuracy Wi-Fi/cellular triangulation (perfect for houses!)
            navigator.geolocation.getCurrentPosition(
                successCallback,
                errorCallback,
                { enableHighAccuracy: false, timeout: 8000, maximumAge: 15000 }
            );
        },
        { enableHighAccuracy: true, timeout: 6000, maximumAge: 0 }
    );
}

function geocodeLeaflet(type) {
    if (isGoogleMaps) return;
    const input = document.getElementById(type + '-location');
    if (!input.value) return;

    fetch(`https://nominatim.openstreetmap.org/search?format=json&email=support@cabbooking.com&email=support@cabbooking.com&q=${encodeURIComponent(input.value)}&countrycodes=in&limit=1`)
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

    // Recalculate based on current route
    if (lastCalculatedDistance > 0) {
        updateFareByDistance(lastCalculatedDistance);
    }
}

async function startSearching() {
    const dropInput = document.getElementById('drop-location');
    const dropText = dropInput.value.trim();
    const pickupInput = document.getElementById('pickup-location');
    const pickupText = pickupInput.value.trim();
    const stopInput = document.getElementById('stop-location');
    const stopText = stopInput ? stopInput.value.trim() : '';
    
    if(!dropText && currentService !== 'rental') return alert('Please enter destination');
    
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
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&email=support@cabbooking.com&email=support@cabbooking.com&q=${encodeURIComponent(pickupText)}&countrycodes=in&limit=1`);
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

    // Fallback: If pickupLatLng is still missing, use current map center
    if (!pickupLatLng && map) {
        const center = map.getCenter();
        pickupLatLng = isGoogleMaps ? center : [center.lat, center.lng];
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
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&email=support@cabbooking.com&email=support@cabbooking.com&q=${encodeURIComponent(stopText)}&countrycodes=in&limit=1`);
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
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&email=support@cabbooking.com&email=support@cabbooking.com&q=${encodeURIComponent(dropText)}&countrycodes=in&limit=1`);
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
    if (pickupLatLng && dropLatLng) {
        calculateRoute();
    }
    
    document.getElementById('location-sheet').classList.add('d-none');
    document.getElementById('center-pin').classList.add('d-none'); 
    document.getElementById('searching-sheet').classList.remove('d-none');
    
    // Perform a second calculation during the searching animation just to be safe
    setTimeout(() => { if (pickupLatLng && dropLatLng) calculateRoute(); }, 1000);

    setTimeout(() => {
        document.getElementById('searching-sheet').classList.add('d-none');
        document.getElementById('ride-options-sheet').classList.remove('d-none');
        
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
    document.getElementById('searching-sheet').classList.add('d-none');
    document.getElementById('location-sheet').classList.remove('d-none');
    document.getElementById('center-pin').classList.remove('d-none'); // Show pin again
    
    // Show service tabs again
    const serviceTabs = document.getElementById('service-tabs-container');
    if (serviceTabs) serviceTabs.classList.remove('d-none');
}

function goBackToLocation() {
    document.getElementById('ride-options-sheet').classList.add('d-none');
    document.getElementById('location-sheet').classList.remove('d-none');
    document.getElementById('center-pin').classList.remove('d-none'); // Show pin again
    
    // Show service tabs again
    const serviceTabs = document.getElementById('service-tabs-container');
    if (serviceTabs) serviceTabs.classList.remove('d-none');
}

let currentBookingId = null;
let bidPollingInterval = null;

function confirmBooking() {
    const pickup = document.getElementById('pickup-location').value;
    const drop = document.getElementById('drop-location').value;
    const totalFareStr = document.getElementById('total-fare').innerText.replace('Rs. ', '').replace('?', '').trim();
    const activeCategory = document.querySelector('.ride-option.active');
    const catId = activeCategory ? activeCategory.getAttribute('data-id') : null;

    let plat = null, plng = null;
    let dlat = null, dlng = null;

    if (pickupLatLng) {
        plat = Array.isArray(pickupLatLng) ? pickupLatLng[0] : (typeof pickupLatLng.lat === 'function' ? pickupLatLng.lat() : pickupLatLng.lat);
        plng = Array.isArray(pickupLatLng) ? pickupLatLng[1] : (typeof pickupLatLng.lng === 'function' ? pickupLatLng.lng() : pickupLatLng.lng);
    }
    if (dropLatLng) {
        dlat = Array.isArray(dropLatLng) ? dropLatLng[0] : (typeof dropLatLng.lat === 'function' ? dropLatLng.lat() : dropLatLng.lat);
        dlng = Array.isArray(dropLatLng) ? dropLatLng[1] : (typeof dropLatLng.lng === 'function' ? dropLatLng.lng() : dropLatLng.lng);
    }

    // Show loading state
    const confirmBtn = event.target;
    const originalText = confirmBtn.innerText;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

    let notesText = 'Preference: ' + acPreference.toUpperCase();
    if (typeof stopLatLng !== 'undefined' && stopLatLng) {
        const stopAddress = document.getElementById('stop-location').value;
        notesText += ' | ?? Stop Point: ' + stopAddress;
    }
    if (currentService === 'parcel') {
        notesText += ' | Parcel Note: ' + document.getElementById('parcel-note').value;
    }

    const formData = {
        _token: '{{ csrf_token() }}',
        pickup_location: pickup,
        dropoff_location: drop,
        pickup_lat: plat,
        pickup_lng: plng,
        dropoff_lat: dlat,
        dropoff_lng: dlng,
        distance: lastCalculatedDistance,
        service_type: currentService,
        vehicle_category_id: catId,
        fare: totalFareStr,
        payment_method: selectedPaymentMethod,
        parcel_details: currentService === 'parcel' ? document.getElementById('parcel-note').value : null,
        notes: notesText
    };

    fetch("{{ route('rider.bookings.store') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentBookingId = data.booking.id;
            document.getElementById('ride-options-sheet').classList.add('d-none');
            document.getElementById('bids-sheet').classList.remove('d-none');
            
            // Start polling for bids
            startBidPolling();
        } else {
            alert(data.message || 'Error creating booking');
            confirmBtn.disabled = false;
            confirmBtn.innerText = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
        confirmBtn.disabled = false;
        confirmBtn.innerText = originalText;
    });
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
            
            // Check if booking has already been accepted/confirmed by a driver (e.g. Instant Accept or accepted bid)
            if (booking && (booking.status === 'accepted' || booking.status === 'arrived' || booking.status === 'ongoing' || booking.status === 'completed')) {
                clearInterval(bidPollingInterval);
                const driver = booking.driver;

                // Update UI to tracking sheet
                document.getElementById('bids-sheet').classList.add('d-none');
                document.getElementById('tracking-sheet').classList.remove('d-none');
                
                if (driver) {
                    document.getElementById('driver-name').innerText = driver.name;
                    document.getElementById('driver-info').innerHTML = `<i class="bi bi-star-fill text-brand"></i> 4.9 Driver • ${driver.vehicle_type || 'Vehicle'}`;
                    document.getElementById('vehicle-reg').innerText = driver.vehicle_number || 'N/A';
                    document.getElementById('final-fare').innerText = `Rs. ${booking.fare}`;
                    document.getElementById('driver-photo').src = driver.profile_image ? `/uploads/profiles/${driver.profile_image}` : `https://i.pravatar.cc/100?u=${driver.id}`;
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
                        <p>Waiting for drivers to bid...</p>
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
                            <h5 class="mb-2 fw-bold text-brand">Rs. ${bid.bid_amount}</h5>
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

function acceptBid(bidId) {
    if (!confirm('Are you sure you want to accept this offer?')) return;

    fetch(`/rider/bookings/bids/${bidId}/accept`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
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
            document.getElementById('final-fare').innerText = `Rs. ${booking.fare}`;
            document.getElementById('driver-photo').src = driver.profile_image ? `/uploads/profiles/${driver.profile_image}` : `https://i.pravatar.cc/100?u=${driver.id}`;
            document.getElementById('driver-phone').href = `tel:${driver.mobile}`;

            if (booking.service_type === 'parcel') {
                document.getElementById('tracking-title').innerText = 'Partner Assigned';
                document.getElementById('parcel-badge').classList.remove('d-none');
            }

            // Start status polling
            startStatusPolling();
            
            // Set chat link
            document.getElementById('chat-driver-link').href = `/chat/${currentBookingId}`;
        } else {
            alert(data.message);
        }
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

            if (status === 'arrived') {
                title.innerText = 'Driver has arrived!';
                desc.innerText = 'Please meet the driver at the pickup point';
                title.className = 'fw-bold mb-1 text-success';
            } else if (status === 'ongoing') {
                title.innerText = 'Ride started';
                title.className = 'fw-bold mb-1 text-info';
                document.querySelector('.ride-controls').classList.add('d-none'); // Hide cancel button
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
function openCancelModal() { document.getElementById('cancel-modal').classList.remove('d-none'); }
function closeCancelModal() { document.getElementById('cancel-modal').classList.add('d-none'); }

function submitCancellation() {
    const reason = document.getElementById('cancel-reason').value;
    
    fetch(`/rider/bookings/${currentBookingId}/cancel`, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message);
        }
    });
}

function rejectBid(bidId) {
    fetch(`/rider/bookings/bids/${bidId}/reject`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fetchBids(); // Refresh bid list
        }
    });
}

function cancelBookingAction() {
    if (!confirm('Are you sure you want to cancel this request?')) return;
    clearInterval(bidPollingInterval);
    document.getElementById('bids-sheet').classList.add('d-none');
    document.getElementById('location-sheet').classList.remove('d-none');
    document.getElementById('center-pin').classList.remove('d-none'); // Show pin again
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
        // Manual fallback if Google Maps hasn't loaded in 5 seconds
        setTimeout(() => {
            if (typeof google === 'undefined') {
                const placeholder = document.getElementById('map-placeholder');
                if (placeholder) placeholder.innerHTML = '<div class="text-dark text-center p-4">Loading map is taking longer than usual...<br><button onclick="location.reload()" class="btn btn-brand btn-sm mt-2">Retry</button></div>';
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











