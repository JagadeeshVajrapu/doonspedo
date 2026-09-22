@extends('layouts.admin')

@section('title', 'Edit Driver')
@section('page_title', 'Update Driver Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.drivers.update', $driver->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control border-light-subtle bg-light bg-opacity-50 @error('name') is-invalid @enderror" value="{{ old('name', $driver->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mobile Number</label>
                            <input type="tel" name="mobile" class="form-control border-light-subtle bg-light bg-opacity-50 @error('mobile') is-invalid @enderror" value="{{ old('mobile', $driver->mobile) }}" required>
                            @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control border-light-subtle bg-light bg-opacity-50 @error('email') is-invalid @enderror" value="{{ old('email', $driver->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select border-light-subtle bg-light bg-opacity-50 @error('vehicle_type') is-invalid @enderror" required>
                                <option value="" disabled>Select vehicle</option>
                                <option value="sedan" {{ old('vehicle_type', $driver->vehicle_type) == 'sedan' ? 'selected' : '' }}>Sedan</option>
                                <option value="suv" {{ old('vehicle_type', $driver->vehicle_type) == 'suv' ? 'selected' : '' }}>SUV</option>
                                <option value="hatchback" {{ old('vehicle_type', $driver->vehicle_type) == 'hatchback' ? 'selected' : '' }}>Hatchback</option>
                                <option value="auto" {{ old('vehicle_type', $driver->vehicle_type) == 'auto' ? 'selected' : '' }}>Auto Rickshaw</option>
                                <option value="bike" {{ old('vehicle_type', $driver->vehicle_type) == 'bike' ? 'selected' : '' }}>Bike</option>
                            </select>
                            @error('vehicle_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Vehicle Number</label>
                            <input type="text" name="vehicle_number" class="form-control border-light-subtle bg-light bg-opacity-50 @error('vehicle_number') is-invalid @enderror" value="{{ old('vehicle_number', $driver->vehicle_number) }}" required>
                            @error('vehicle_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">License Number</label>
                            <input type="text" name="license_number" class="form-control border-light-subtle bg-light bg-opacity-50 @error('license_number') is-invalid @enderror" value="{{ old('license_number', $driver->license_number) }}" required>
                            @error('license_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" name="city" class="form-control border-light-subtle bg-light bg-opacity-50 @error('city') is-invalid @enderror" value="{{ old('city', $driver->city) }}" required>
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mt-4 d-flex gap-3">
                            <a href="{{ route('admin.drivers.index') }}" class="btn btn-light w-50 py-3 fw-bold">Cancel</a>
                            <button type="submit" class="btn btn-brand w-50 py-3 fw-bold">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
