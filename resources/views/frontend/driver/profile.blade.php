@extends('layouts.driver')

@section('title', 'Manage Profile - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Profile</h1>
        <p class="text-muted mb-0 small">Manage your account information and preferences.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="drv-card overflow-hidden mb-4">
            <div class="py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="h5 fw-bold mb-0">Personal information</h2>
                @include('partials.ui.status-badge', [
                    'label' => ucfirst($driver->status),
                    'variant' => $driver->status == 'approved' ? 'success' : ($driver->status == 'rejected' ? 'danger' : 'warning'),
                ])
            </div>
            
            @if(session('success'))
                <div class="mx-4 mt-3">
                    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
                </div>
            @endif

            <div class="p-4">
                <form action="{{ route('driver.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <!-- Profile Photo -->
                        <div class="col-12 text-center mb-2">
                            <div class="position-relative d-inline-block">
                                @if($driver->profile_image)
                                    <img src="{{ media_url($driver->profile_image) }}" class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover;" alt="{{ $driver->name }}">
                                @else
                                    <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 120px; height: 120px;">
                                        <i class="bi bi-person text-secondary display-4"></i>
                                    </div>
                                @endif
                                <label for="profile_image" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 rounded-circle p-2 border border-2 border-white cursor-pointer" aria-label="Change profile photo">
                                    <i class="bi bi-camera"></i>
                                </label>
                                <input type="file" name="profile_image" id="profile_image" class="d-none" accept="image/*">
                            </div>
                            <p class="small text-muted mt-2 fw-bold">Profile photo</p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $driver->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Mobile Number (Locked)</label>
                            <input type="text" class="form-control rounded-3 bg-light" value="{{ $driver->mobile }}" readonly disabled>
                            <small class="text-muted">Contact support to change mobile number.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                            <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $driver->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Operating City</label>
                            <input type="text" name="city" class="form-control rounded-3" value="{{ old('city', $driver->city) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select rounded-3" required>
                                <option value="sedan" {{ $driver->vehicle_type == 'sedan' ? 'selected' : '' }}>Sedan</option>
                                <option value="suv" {{ $driver->vehicle_type == 'suv' ? 'selected' : '' }}>SUV</option>
                                <option value="hatchback" {{ $driver->vehicle_type == 'hatchback' ? 'selected' : '' }}>Hatchback</option>
                                <option value="auto" {{ $driver->vehicle_type == 'auto' ? 'selected' : '' }}>Auto Rickshaw</option>
                                <option value="bike" {{ $driver->vehicle_type == 'bike' ? 'selected' : '' }}>Bike</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehicle Number</label>
                            <input type="text" name="vehicle_number" class="form-control rounded-3" value="{{ old('vehicle_number', $driver->vehicle_number) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Driving License Number</label>
                            <input type="text" name="license_number" class="form-control rounded-3" value="{{ old('license_number', $driver->license_number) }}" required>
                        </div>
                        
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-bold shadow-sm">
                                SAVE CHANGES
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <div class="text-center mb-4">
                <div class="bg-light p-4 rounded-circle d-inline-block mb-3">
                    <i class="bi bi-shield-lock text-brand display-4"></i>
                </div>
                <h6 class="fw-bold">Security & Verification</h6>
                <p class="text-muted small">Your account security is important to us. Keep your documents verified to maintain active status.</p>
            </div>
            <hr class="opacity-10">
            <div class="d-grid gap-2">
                <a href="{{ route('driver.kyc') }}" class="btn btn-outline-dark rounded-pill fw-bold">
                    <i class="bi bi-file-earmark-text me-2"></i> Manage Documents
                </a>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4">
            <h6 class="fw-bold mb-2">Need Support?</h6>
            <p class="small text-secondary mb-3">If you face any issues with your profile or account, feel free to contact us.</p>
            <a href="#" class="btn btn-brand btn-sm rounded-pill fw-bold px-4">Contact Support</a>
        </div>
    </div>
</div>

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
</style>

<script>
document.getElementById('profile_image').onchange = function (evt) {
    const [file] = this.files;
    if (file) {
        const preview = document.querySelector('.card-body img') || document.querySelector('.card-body .bg-secondary');
        if (preview.tagName === 'IMG') {
            preview.src = URL.createObjectURL(file);
        } else {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'rounded-circle shadow';
            img.style.width = '120px';
            img.style.height = '120px';
            img.style.objectFit = 'cover';
            preview.replaceWith(img);
        }
    }
}
</script>
@endsection
