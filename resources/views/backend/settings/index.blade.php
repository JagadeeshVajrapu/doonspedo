@extends('layouts.admin')

@section('title', 'System Settings')
@section('page_title', 'Application Configuration')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders text-brand me-2"></i> System Settings</h5>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4 px-4 py-3">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- Vertical Tabs menu -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="nav flex-column nav-pills custom-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link text-start active fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab">
                            <i class="bi bi-display me-2"></i> General Info
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-layout-tab" data-bs-toggle="pill" data-bs-target="#v-pills-layout" type="button" role="tab">
                            <i class="bi bi-layout-sidebar-reverse me-2"></i> Theme & RTL
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-map-tab" data-bs-toggle="pill" data-bs-target="#v-pills-map" type="button" role="tab">
                            <i class="bi bi-geo-alt me-2"></i> Map & Location API
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-lang-tab" data-bs-toggle="pill" data-bs-target="#v-pills-lang" type="button" role="tab">
                            <i class="bi bi-translate me-2"></i> Multi-Language
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-currency-tab" data-bs-toggle="pill" data-bs-target="#v-pills-currency" type="button" role="tab">
                            <i class="bi bi-cash-stack me-2"></i> Multi-Currency
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-sms-tab" data-bs-toggle="pill" data-bs-target="#v-pills-sms" type="button" role="tab">
                            <i class="bi bi-chat-left-text me-2"></i> SMS Settings
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 rounded-3" id="v-pills-payment-tab" data-bs-toggle="pill" data-bs-target="#v-pills-payment" type="button" role="tab">
                            <i class="bi bi-credit-card me-2"></i> Payment Gateways
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Content -->
        <div class="col-md-9">
            <div class="admin-card">
                <div class="card-body p-5">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- General Settings Panel -->
                        <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-display text-muted me-2"></i> General Identity</h5>
                            
                            <div class="row g-4 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Application Name</label>
                                    <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? 'Doonspedo' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Support Phone Number</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Support Email</label>
                                    <input type="email" name="support_email" class="form-control" value="{{ $settings['support_email'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Timezone</label>
                                    <select name="timezone" class="form-select">
                                        <option value="UTC" {{ ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="America/New_York" {{ ($settings['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>EST (America/New_York)</option>
                                        <option value="Europe/London" {{ ($settings['timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>GMT (Europe/London)</option>
                                        <option value="Asia/Kolkata" {{ ($settings['timezone'] ?? '') == 'Asia/Kolkata' ? 'selected' : '' }}>IST (Asia/Kolkata)</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-4">
                                    <label class="form-label small fw-bold text-muted">App Logo / Brand Icon</label>
                                    <div class="d-flex align-items-center bg-light p-4 rounded-4 border border-dashed text-center justify-content-center" style="cursor: pointer;">
                                        @if(isset($settings['app_logo']))
                                            <div class="me-4 text-start">
                                                @include('partials.ui.brand-logo', ['logo' => $settings['app_logo'], 'size' => 'sm', 'alt' => 'Logo'])
                                                <div class="small text-muted mt-2">Current Logo</div>
                                            </div>
                                        @endif
                                        <div class="w-100 flex-grow-1 text-center">
                                            <input type="file" name="app_logo" class="form-control" accept="image/png, image/jpeg, image/webp">
                                            <small class="text-muted mt-2 d-block">Recommended size 200x60 transparent PNG</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-4 bg-light rounded-4 border">
                                <h5 class="fw-bold mb-1 text-dark">Dehradun Local Fare</h5>
                                <p class="small text-muted">These rates apply only to rides that start in Dehradun and stay within the maximum local distance. Trips farther than that limit cannot be booked on the local rate. Other cities keep their existing vehicle rates.</p>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold" for="dehradun-bike-rate">Bike rate per KM</label>
                                        <input type="number" id="dehradun-bike-rate" name="dehradun_bike_rate_per_km" class="form-control" min="0" max="10000" step="0.01" value="{{ $settings['dehradun_bike_rate_per_km'] ?? 8 }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold" for="dehradun-auto-rate">Auto rate per KM</label>
                                        <input type="number" id="dehradun-auto-rate" name="dehradun_auto_rate_per_km" class="form-control" min="0" max="10000" step="0.01" value="{{ $settings['dehradun_auto_rate_per_km'] ?? 12 }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold" for="dehradun-car-rate">Car rate per KM</label>
                                        <input type="number" id="dehradun-car-rate" name="dehradun_car_rate_per_km" class="form-control" min="0" max="10000" step="0.01" value="{{ $settings['dehradun_car_rate_per_km'] ?? 20 }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold" for="dehradun-max-km">Maximum local distance (KM)</label>
                                        <input type="number" id="dehradun-max-km" name="dehradun_max_local_km" class="form-control" min="0.1" max="500" step="0.1" value="{{ $settings['dehradun_max_local_km'] ?? 40 }}" required>
                                    </div>
                                </div>
                                <div class="form-check mt-3">
                                    <input type="hidden" name="customer_kyc_required" value="0">
                                    <input class="form-check-input" type="checkbox" name="customer_kyc_required" id="customer-kyc-required" value="1" {{ ($settings['customer_kyc_required'] ?? '0') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="customer-kyc-required">Require verified customer KYC before a new booking</label>
                                </div>
                            </div>
                        </div>

                        <!-- Theme & RTL Panel -->
                        <div class="tab-pane fade" id="v-pills-layout" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-layout-sidebar-reverse text-muted me-2"></i> Theme & RTL Configuration</h5>
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center border-bottom border-light pb-4">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">Enable RTL Direction (Right-to-Left)</h6>
                                            <p class="text-muted small mb-0">Toggle global alignment for Arabic, Hebrew, or Urdu environments.</p>
                                        </div>
                                        <div class="form-check form-switch fs-4">
                                            <input class="form-check-input" type="checkbox" name="rtl_enabled" id="rtlSwitch" {{ !empty($settings['rtl_enabled']) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <label class="form-label small fw-bold text-muted">Primary Brand Color HEX</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color border-end-0 border" name="theme_color" value="{{ $settings['theme_color'] ?? '#ffc107' }}" title="Choose primary color">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Map & API Panel -->
                        <div class="tab-pane fade" id="v-pills-map" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-geo-alt text-muted me-2"></i> Map Engine Configuration</h5>
                            
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Active Map Provider</label>
                                <div class="row g-3">
                                    <!-- Google Maps Option -->
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="map_provider" id="googleMap" value="google" {{ ($settings['map_provider'] ?? 'google') == 'google' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100 py-3 rounded-4 shadow-sm text-start" for="googleMap">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-google fs-3 me-3"></i>
                                                <div>
                                                    <h6 class="fw-bold mb-0">Google Maps API</h6>
                                                    <small class="opacity-75">Requires valid Billing Setup</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <!-- OSM Option -->
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="map_provider" id="osmMap" value="osm" {{ ($settings['map_provider'] ?? '') == 'osm' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100 py-3 rounded-4 shadow-sm text-start" for="osmMap">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-globe fs-3 me-3"></i>
                                                <div>
                                                    <h6 class="fw-bold mb-0">OpenStreetMap (OSM)</h6>
                                                    <small class="opacity-75">Free fallback provider / Leaflet</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4 p-4 bg-light rounded-4 border">
                                <label class="form-label fw-bold text-dark" for="max-driver-acceptance-km">Maximum Driver Acceptance Distance</label>
                                <div class="input-group" style="max-width: 220px;">
                                    <input type="number" id="max-driver-acceptance-km" name="max_driver_acceptance_km" class="form-control" min="0.1" max="100" step="0.1" value="{{ $settings['max_driver_acceptance_km'] ?? 3 }}" required>
                                    <span class="input-group-text">KM</span>
                                </div>
                                <div class="form-text small mt-2">Drivers farther than this from the pickup do not receive the ride. Applies to every driver.</div>
                            </div>

                            <div class="mb-3 p-4 bg-light rounded-4 border">
                                <label class="form-label fw-bold text-dark">Google Maps API Key</label>
                                <input type="text" name="google_maps_key" class="form-control font-monospace text-muted" value="{{ $settings['google_maps_key'] ?? '' }}" placeholder="AIzaSyB*************************">
                                <div class="form-text small mt-2">Required for autocomplete, routing, and live driver tracking if "Google Maps" is active.</div>
                            </div>
                        </div>

                        <!-- Language Panel -->
                        <div class="tab-pane fade" id="v-pills-lang" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-translate text-muted me-2"></i> Localization & Languages</h5>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Default System Language</label>
                                    <select name="default_language" class="form-select">
                                        <option value="en" {{ ($settings['default_language'] ?? 'en') == 'en' ? 'selected' : '' }}>English (EN)</option>
                                        <option value="es" {{ ($settings['default_language'] ?? '') == 'es' ? 'selected' : '' }}>Spanish (ES)</option>
                                        <option value="fr" {{ ($settings['default_language'] ?? '') == 'fr' ? 'selected' : '' }}>French (FR)</option>
                                        <option value="ar" {{ ($settings['default_language'] ?? '') == 'ar' ? 'selected' : '' }}>Arabic (AR)</option>
                                    </select>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <label class="form-label small fw-bold text-muted">User-Selectable Languages (Multi-Language Mode)</label>
                                    <div class="d-flex flex-wrap gap-4 mt-2 p-4 bg-light rounded-4 border">
                                        <div class="form-check form-switch fs-5">
                                            <input class="form-check-input" type="checkbox" id="lang_en" checked disabled>
                                            <label class="form-check-label ms-2 fs-6 fw-bold" for="lang_en">English (Default)</label>
                                        </div>
                                        <div class="form-check form-switch fs-5">
                                            <input class="form-check-input" type="checkbox" name="supported_languages[]" value="es" id="lang_es" {{ in_array('es', (array)($settings['supported_languages'] ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2 fs-6" for="lang_es">Spanish</label>
                                        </div>
                                        <div class="form-check form-switch fs-5">
                                            <input class="form-check-input" type="checkbox" name="supported_languages[]" value="fr" id="lang_fr" {{ in_array('fr', (array)($settings['supported_languages'] ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2 fs-6" for="lang_fr">French</label>
                                        </div>
                                        <div class="form-check form-switch fs-5">
                                            <input class="form-check-input" type="checkbox" name="supported_languages[]" value="ar" id="lang_ar" {{ in_array('ar', (array)($settings['supported_languages'] ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2 fs-6" for="lang_ar">Arabic (RTL)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Currency Panel -->
                        <div class="tab-pane fade" id="v-pills-currency" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-cash-stack text-muted me-2"></i> Fiat & Currency Config</h5>

                            <div class="d-flex align-items-center mb-4 p-4 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-primary mb-1">Dynamic Multi-Currency Support</h6>
                                    <p class="text-muted small mb-0">Allow users to view fares and add wallet balances dynamically via live FX rates.</p>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <input class="form-check-input border-primary" type="checkbox" name="multi_currency_enabled" id="multiCurrencySwitch" {{ !empty($settings['multi_currency_enabled']) ? 'checked' : '' }}>
                                </div>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Base / Default System Currency</label>
                                    <select name="currency" class="form-select text-dark fw-bold">
                                        <option value="USD" {{ ($settings['currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>$ USD - US Dollar</option>
                                        <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>€ EUR - Euro</option>
                                        <option value="GBP" {{ ($settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>£ GBP - British Pound</option>
                                        <option value="INR" {{ ($settings['currency'] ?? '') == 'INR' ? 'selected' : '' }}>₹ INR - Indian Rupee</option>
                                        <option value="SAR" {{ ($settings['currency'] ?? '') == 'SAR' ? 'selected' : '' }}>﷼ SAR - Saudi Riyal</option>
                                    </select>
                                    <div class="form-text small">All ride calculations and gateway deposits anchor to this fiat.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Value Formatting</label>
                                    <select name="currency_format" class="form-select">
                                        <option value="left">Symbol Left ({{ $default_currency->symbol ?? '₹' }}100)</option>
                                        <option value="right">Symbol Right (100$)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                         <!-- SMS Settings Panel -->
                        <div class="tab-pane fade" id="v-pills-sms" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-chat-left-text text-muted me-2"></i> SMS Gateways</h5>
                            
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Select SMS Provider</label>
                                <select name="sms_provider" class="form-select mb-4">
                                    <option value="none" {{ ($settings['sms_provider'] ?? '') == 'none' ? 'selected' : '' }}>Disabled</option>
                                    <option value="truebulksms" {{ ($settings['sms_provider'] ?? '') == 'truebulksms' ? 'selected' : '' }}>TrueBulkSMS (Indian Provider)</option>
                                </select>
                            </div>

                            <!-- TrueBulkSMS Configuration Card -->
                            <div id="truebulksms_config" class="config-pane {{ ($settings['sms_provider'] ?? '') == 'truebulksms' ? '' : 'd-none' }}">
                                <div class="card border border-light-subtle rounded-4 shadow-sm">
                                    <div class="card-header bg-light py-3">
                                        <h6 class="mb-0 fw-bold"><i class="bi bi-gear-fill me-2 text-primary"></i> TrueBulkSMS Settings</h6>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted">Username</label>
                                                <input type="text" name="true_bulk_sms_username" class="form-control" value="{{ $settings['true_bulk_sms_username'] ?? '' }}" placeholder="Enter your TrueBulkSMS Username">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted">Password</label>
                                                <input type="password" name="true_bulk_sms_password" class="form-control" value="{{ $settings['true_bulk_sms_password'] ?? '' }}" placeholder="Enter your TrueBulkSMS Password">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted">Sender ID</label>
                                                <input type="text" name="true_bulk_sms_sender_id" class="form-control" value="{{ $settings['true_bulk_sms_sender_id'] ?? '' }}" placeholder="e.g. TXTSMS">
                                                <div class="form-text">Approved 6-character Header/Sender ID.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted">Entity ID (DLT)</label>
                                                <input type="text" name="true_bulk_sms_entity_id" class="form-control" value="{{ $settings['true_bulk_sms_entity_id'] ?? '' }}" placeholder="Principal Entity ID">
                                                <div class="form-text">Required for DLT compliance in India.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted">Template ID (DLT)</label>
                                                <input type="text" name="true_bulk_sms_temp_id" class="form-control" value="{{ $settings['true_bulk_sms_temp_id'] ?? '' }}" placeholder="e.g. 1207161... ">
                                                <div class="form-text">Specify the DLT Template ID for sending messages.</div>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <hr class="opacity-25">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold small text-dark">Send Test Message</h6>
                                                        <p class="text-muted small mb-0">Validate your credentials and Template ID.</p>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <input type="text" id="test_mobile" class="form-control form-control-sm" style="width: 150px;" placeholder="Mobile Number">
                                                        <button type="button" onclick="sendTestSMS()" class="btn btn-outline-brand btn-sm rounded-pill px-3">
                                                            <i class="bi bi-send me-1"></i> Quick Test
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="none_config" class="config-pane {{ ($settings['sms_provider'] ?? 'none') == 'none' ? '' : 'd-none' }}">
                                <div class="alert alert-info rounded-4 border-0">
                                    <i class="bi bi-info-circle-fill me-2"></i> SMS service is currently disabled. Select a provider to configure settings.
                                </div>
                            </div>
                        </div>

                        <!-- Payment Settings Panel -->
                        <div class="tab-pane fade" id="v-pills-payment" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-credit-card text-muted me-2"></i> Payment Gateway Configuration</h5>
                            
                            <div class="row g-4">
                                <!-- Razorpay Section -->
                                <div class="col-md-6">
                                    <div class="card border border-light-subtle rounded-4 shadow-sm h-100">
                                        <div class="card-header bg-white py-3 border-bottom border-light">
                                            <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-primary"></i> Razorpay (India)</h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">Razorpay Key ID</label>
                                                <input type="text" name="razorpay_key" class="form-control font-monospace" value="{{ $settings['razorpay_key'] ?? '' }}" placeholder="rzp_live_...">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small fw-bold text-muted">Razorpay Secret Key</label>
                                                <input type="password" name="razorpay_secret" class="form-control font-monospace" value="{{ $settings['razorpay_secret'] ?? '' }}" placeholder="••••••••••••••••">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PayPal Section -->
                                <div class="col-md-6">
                                    <div class="card border border-light-subtle rounded-4 shadow-sm h-100">
                                        <div class="card-header bg-white py-3 border-bottom border-light">
                                            <h6 class="mb-0 fw-bold"><i class="bi bi-paypal me-2 text-info"></i> PayPal (Global)</h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">PayPal Client ID</label>
                                                <input type="text" name="paypal_id" class="form-control font-monospace" value="{{ $settings['paypal_id'] ?? '' }}" placeholder="AcB123...">
                                            </div>
                                            <div class="form-text small">
                                                <i class="bi bi-info-circle me-1"></i> Ensure your business email is verified in PayPal dashboard.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Manual Payment Section -->
                                <div class="col-md-12">
                                    <div class="card border border-light-subtle rounded-4 shadow-sm">
                                        <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold"><i class="bi bi-bank me-2 text-warning"></i> Manual Payment / Bank Transfer</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="manual_payment_enabled" id="manualPaymentSwitch" {{ !empty($settings['manual_payment_enabled']) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold text-muted" for="manualPaymentSwitch">Enable</label>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold text-muted">Payment Instructions (Bank Details, UPI, etc.)</label>
                                                    <textarea name="manual_payment_instructions" class="form-control" rows="4" placeholder="Enter instructions for users to make manual payments (e.g., Bank Name, A/C Number, IFSC, or UPI ID)">{{ $settings['manual_payment_instructions'] ?? '' }}</textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-muted">Payment QR Code</label>
                                                    <div class="mt-1">
                                                        @if(isset($settings['manual_payment_qr']))
                                                            <div class="mb-2">
                                                                <img src="{{ asset($settings['manual_payment_qr']) }}" alt="QR Code" class="img-thumbnail" style="max-height: 120px;">
                                                            </div>
                                                        @endif
                                                        <input type="file" name="manual_payment_qr" class="form-control form-control-sm" accept="image/*">
                                                        <div class="form-text extra-small mt-1">Upload UPI QR for easy mobile payments.</div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <div class="form-text small text-muted">
                                                        <i class="bi bi-lightbulb me-1"></i> These details will be shown to the user when they select 'Manual Payment' during checkout.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="alert alert-warning rounded-4 border-0 small">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Security Note:</strong> Always keep your API secrets private. These keys are used to process real financial transactions.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="card-footer bg-white border-top border-light p-4 text-end rounded-bottom-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm py-2">
                        <i class="bi bi-check2-circle me-1"></i> Save Changes to System
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
/* Custom Pill styles for Settings Vertical Tabs */
.custom-pills .nav-link {
    color: #495057;
    background-color: transparent;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}
.custom-pills .nav-link:hover {
    background-color: rgba(0,0,0,0.03);
    color: #121212;
}
.custom-pills .nav-link.active {
    background-color: #f8f9fa;
    color: var(--bs-primary);
    border-left: 3px solid var(--bs-primary);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const providerSelect = document.querySelector('select[name="sms_provider"]');
    if (providerSelect) {
        providerSelect.addEventListener('change', function() {
            const provider = this.value;
            // Hide all config panes
            document.querySelectorAll('.config-pane').forEach(pane => {
                pane.classList.add('d-none');
            });
            // Show selected config pane or none
            const activePane = document.getElementById(provider + '_config');
            if (activePane) {
                activePane.classList.remove('d-none');
            } else if (provider === 'none') {
                document.getElementById('none_config').classList.remove('d-none');
            }
        });
    }
});

function sendTestSMS() {
    const mobile = document.getElementById('test_mobile').value;
    if (!mobile) {
        alert('Please enter a mobile number first.');
        return;
    }

    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
    btn.disabled = true;

    fetch("{{ route('admin.settings.test_sms') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ mobile: mobile })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending test SMS.');
    })
    .finally(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}
</script>
@endsection
