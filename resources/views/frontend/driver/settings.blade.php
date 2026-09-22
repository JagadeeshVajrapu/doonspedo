@extends('layouts.driver')

@section('title', 'Settings - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Settings</h1>
        <p class="text-muted mb-0 small">Customize your app experience, language, and currency.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('driver.settings.update') }}" method="POST">
            @csrf
            
            <!-- Language & Locale -->
            <div class="drv-card overflow-hidden mb-4">
                <div class="py-3 px-4 border-bottom">
                    <h2 class="h6 fw-bold mb-0"><i class="bi bi-translate text-primary me-2"></i> Language & localization</h2>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Select language</label>
                            <div class="row g-2">
                                @foreach($languages as $lang)
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="locale" id="lang_{{ $lang->code }}" value="{{ $lang->code }}" {{ $driver->locale == $lang->code ? 'checked' : '' }}>
                                    <label class="btn btn-outline-light text-dark border p-3 w-100 rounded-3 text-start d-flex align-items-center justify-content-between" for="lang_{{ $lang->code }}">
                                        <div>
                                            <div class="fw-bold">{{ $lang->name }}</div>
                                            <div class="small text-muted">{{ strtoupper($lang->code) }} - {{ strtoupper($lang->direction) }}</div>
                                        </div>
                                        @if($driver->locale == $lang->code)
                                            <i class="bi bi-check-circle-fill text-primary"></i>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currency Settings -->
            <div class="drv-card overflow-hidden mb-4">
                <div class="py-3 px-4 border-bottom">
                    <h2 class="h6 fw-bold mb-0"><i class="bi bi-currency-exchange text-success me-2"></i> Preferred currency</h2>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Select currency</label>
                            <select name="currency_code" class="form-select rounded-3 p-3">
                                @foreach($currencies as $curr)
                                    <option value="{{ $curr->code }}" {{ $driver->currency_code == $curr->code ? 'selected' : '' }}>
                                        {{ $curr->name }} ({{ $curr->symbol }}) - {{ $curr->code }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="small text-muted mt-2 mb-0">Earnings and balances will be displayed in the selected currency based on current exchange rates.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UI Theme -->
            <div class="drv-card overflow-hidden mb-4">
                <div class="py-3 px-4 border-bottom">
                    <h2 class="h6 fw-bold mb-0"><i class="bi bi-palette text-warning me-2"></i> Interface theme</h2>
                </div>
                <div class="p-4">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="theme" id="theme_light" value="light" {{ $driver->theme == 'light' ? 'checked' : '' }}>
                            <label class="btn btn-outline-light text-dark border p-4 w-100 rounded-4 text-center" for="theme_light">
                                <i class="bi bi-sun-fill display-6 mb-2 d-block text-warning"></i>
                                <div class="fw-bold">Light mode</div>
                                <div class="small text-muted">Clear and bright interface</div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="theme" id="theme_dark" value="dark" {{ $driver->theme == 'dark' ? 'checked' : '' }}>
                            <label class="btn btn-outline-light text-dark border p-4 w-100 rounded-4 text-center" for="theme_dark">
                                <i class="bi bi-moon-stars-fill display-6 mb-2 d-block text-primary"></i>
                                <div class="fw-bold">Dark mode</div>
                                <div class="small text-muted">Reduced eye strain at night</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid mb-5">
                <button type="submit" class="btn btn-brand py-3 rounded-pill fw-bold shadow">Save all changes</button>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="drv-card bg-dark text-white p-4 sticky-top" style="top: 20px;">
            <h2 class="h5 fw-bold mb-3">Settings guide</h2>
            <div class="vstack gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-info-circle small"></i>
                    </div>
                    <div>
                        <h3 class="h6 mb-1 fw-bold">RTL support</h3>
                        <p class="small text-white-50 mb-0">Languages like Arabic will automatically flip the app layout for better readability.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-clock small"></i>
                    </div>
                    <div>
                        <h3 class="h6 mb-1 fw-bold">Instant update</h3>
                        <p class="small text-white-50 mb-0">Theme and language changes take effect immediately after saving.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-outline-light:hover { background-color: #f8f9fa; }
.btn-check:checked + .btn-outline-light { border-color: var(--admin-primary) !important; background-color: rgba(205, 220, 41, 0.05); }
.btn-brand { background-color: #cddc29; color: #000; }
.btn-brand:hover { background-color: #b9c825; }

/* Dark mode overrides for settings page */
.dark-mode .btn-outline-light { border-color: #333 !important; color: #fff !important; }
.dark-mode .btn-outline-light:hover { background-color: #2a2a2a; }
.dark-mode .btn-check:checked + .btn-outline-light { border-color: #cddc29 !important; background-color: rgba(205, 220, 41, 0.1); }
</style>
@endsection
