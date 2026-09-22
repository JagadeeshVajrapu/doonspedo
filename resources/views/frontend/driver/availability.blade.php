@extends('layouts.driver')

@section('title', 'Availability & Controls - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Availability</h1>
        <p class="text-muted mb-0 small">Manage when you are available and your ride preferences.</p>
    </div>
    <div class="form-check form-switch bg-white shadow-sm p-3 rounded-4 border d-flex align-items-center gap-3 {{ $driver->is_online ? 'border-success' : 'border-secondary' }}">
        <label class="form-check-label fw-bold mb-0" for="onDutyToggle">
            <span id="onDutyLabel" class="{{ $driver->is_online ? 'text-success' : 'text-danger' }}">
                {{ $driver->is_online ? 'ON DUTY' : 'OFF DUTY' }}
            </span>
        </label>
        <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="onDutyToggle" {{ $driver->is_online ? 'checked' : '' }} style="width: 3.5em; height: 1.75em; cursor: pointer;" aria-checked="{{ $driver->is_online ? 'true' : 'false' }}" aria-describedby="onDutyLabel">
    </div>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<form action="{{ route('driver.availability.update') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Working Hours -->
        <div class="col-lg-7">
            <div class="drv-card overflow-hidden h-100">
                <div class="py-3 px-4 border-bottom">
                    <h2 class="h5 fw-bold mb-0"><i class="bi bi-clock-history text-brand me-2"></i> Working hours</h2>
                </div>
                <div class="p-4">
                    <p class="small text-muted mb-4">Set your preferred operating hours for each day. You will only receive requests during these times when your status is "On Duty".</p>
                    
                    <div class="vstack gap-3">
                        @php
                            $workingDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            $hoursData = (array) ($driver->working_hours ?? []);
                        @endphp
                        
                        @foreach($workingDays as $day)
                        <div class="row align-items-center bg-light bg-opacity-50 p-3 rounded-4 g-2">
                            <div class="col-md-3">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="working_hours[{{ $day }}][enabled]" value="1" id="switch_{{ $day }}" {{ data_get($hoursData, $day . '.enabled') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="switch_{{ $day }}">{{ ucfirst($day) }}</label>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex align-items-center gap-2 day-inputs-{{ $day }} {{ !data_get($hoursData, $day . '.enabled') ? 'opacity-50' : '' }}">
                                    <input type="time" name="working_hours[{{ $day }}][start]" class="form-control rounded-3 border-0 shadow-sm" value="{{ data_get($hoursData, $day . '.start', '09:00') }}" {{ !data_get($hoursData, $day . '.enabled') ? 'disabled' : '' }}>
                                    <span class="text-muted small">to</span>
                                    <input type="time" name="working_hours[{{ $day }}][end]" class="form-control rounded-3 border-0 shadow-sm" value="{{ data_get($hoursData, $day . '.end', '17:00') }}" {{ !data_get($hoursData, $day . '.enabled') ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Ride Preferences -->
        <div class="col-lg-5">
            <div class="drv-card overflow-hidden mb-4">
                <div class="py-3 px-4 border-bottom">
                    <h2 class="h5 fw-bold mb-0"><i class="bi bi-sliders text-brand me-2"></i> Ride preferences</h2>
                </div>
                <div class="p-4">
                    @php $prefsData = (array) ($driver->ride_preferences ?? []) @endphp
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-3">Maximum Request Distance (KM)</label>
                        <div class="px-2">
                            <input type="range" name="ride_preferences[max_distance]" class="form-range custom-range" min="1" max="100" value="{{ data_get($prefsData, 'max_distance', 20) }}" id="maxDistanceRange">
                            <div class="d-flex justify-content-between mt-2">
                                <span class="small fw-bold text-muted">1 KM</span>
                                <span class="h5 fw-bold text-brand"><span id="rangeValue">{{ data_get($prefsData, 'max_distance', 20) }}</span> KM</span>
                                <span class="small fw-bold text-muted">100 KM</span>
                            </div>
                        </div>
                    </div>

                    <div class="list-group list-group-flush border-top pt-3">
                        <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0">Accept Cash Payments</h6>
                                <p class="text-muted small mb-0">Enable if you want to receive rides with cash payments.</p>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="ride_preferences[accept_cash]" value="1" {{ data_get($prefsData, 'accept_cash', true) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0">Outstation Rides</h6>
                                <p class="text-muted small mb-0">Receive requests for inter-city long distance trips.</p>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="ride_preferences[accept_outstation]" value="1" {{ data_get($prefsData, 'accept_outstation', false) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0">Auto-Accept Requests</h6>
                                <p class="text-muted small mb-0">Automatically accept rides that match your criteria.</p>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="ride_preferences[auto_accept]" value="1" {{ data_get($prefsData, 'auto_accept', false) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-bold shadow-sm mb-4">
                <i class="bi bi-save2 me-2"></i> SAVE ALL SETTINGS
            </button>

            <div class="drv-card bg-dark text-white p-4">
                <h2 class="h6 fw-bold mb-2">Tip</h2>
                <p class="small text-white-50 mb-0">Peak hours often bring more requests. Keep your schedule updated so you receive the right jobs.</p>
            </div>
        </div>
    </div>
</form>

<style>
.btn-brand {
    background-color: #cddc29;
    color: #000;
}
.btn-brand:hover {
    background-color: #b9c825;
    color: #000;
}
.text-brand {
    color: #cddc29 !important;
}
.custom-range::-webkit-slider-thumb {
    background: #cddc29;
}
.custom-range::-moz-range-thumb {
    background: #cddc29;
}
.form-check-input:checked {
    background-color: #cddc29;
    border-color: #cddc29;
}
#onDutyToggle.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}
#onDutyToggle.form-check-input {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>

@section('scripts')
<script>
    // Range Slider Value Display
    const rangeInput = document.getElementById('maxDistanceRange');
    const rangeValue = document.getElementById('rangeValue');
    rangeInput.addEventListener('input', () => {
        rangeValue.textContent = rangeInput.value;
    });

    // Day Toggle Logic
    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
    document.getElementById('switch_{{ $day }}').addEventListener('change', function() {
        const inputs = document.querySelector('.day-inputs-{{ $day }}');
        const timeInputs = inputs.querySelectorAll('input');
        if (this.checked) {
            inputs.classList.remove('opacity-50');
            timeInputs.forEach(input => input.disabled = false);
        } else {
            inputs.classList.add('opacity-50');
            timeInputs.forEach(input => input.disabled = true);
        }
    });
    @endforeach

    // Online/Offline Toggle via AJAX
    document.getElementById('onDutyToggle').addEventListener('change', function() {
        const isOn = this.checked;
        const label = document.getElementById('onDutyLabel');
        
        fetch("{{ route('driver.toggleStatus') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                label.textContent = data.is_online ? 'ON DUTY' : 'OFF DUTY';
                label.className = data.is_online ? 'text-success' : 'text-danger';
                this.checked = data.is_online;
                this.setAttribute('aria-checked', data.is_online ? 'true' : 'false');
            } else {
                alert(data.message || 'Failed to update status.');
                this.checked = !isOn;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong.');
            this.checked = !isOn;
        });
    });
</script>
@endsection
@endsection
