@extends('layouts.admin')

@section('title', 'Create Driver')
@section('page_title', 'Add New Driver')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.drivers.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control border-light-subtle bg-light bg-opacity-50 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter driver name" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mobile Number</label>
                            <input type="tel" name="mobile" class="form-control border-light-subtle bg-light bg-opacity-50 @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" placeholder="+91 00000 00000" required>
                            @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control border-light-subtle bg-light bg-opacity-50 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="driver@example.com" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select border-light-subtle bg-light bg-opacity-50 @error('vehicle_type') is-invalid @enderror" required>
                                <option value="" disabled selected>Select vehicle</option>
                                <option value="sedan" {{ old('vehicle_type') == 'sedan' ? 'selected' : '' }}>Sedan</option>
                                <option value="suv" {{ old('vehicle_type') == 'suv' ? 'selected' : '' }}>SUV</option>
                                <option value="hatchback" {{ old('vehicle_type') == 'hatchback' ? 'selected' : '' }}>Hatchback</option>
                                <option value="auto" {{ old('vehicle_type') == 'auto' ? 'selected' : '' }}>Auto Rickshaw</option>
                                <option value="bike" {{ old('vehicle_type') == 'bike' ? 'selected' : '' }}>Bike</option>
                            </select>
                            @error('vehicle_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Vehicle Number</label>
                            <input type="text" name="vehicle_number" class="form-control border-light-subtle bg-light bg-opacity-50 @error('vehicle_number') is-invalid @enderror" value="{{ old('vehicle_number') }}" placeholder="DL 01 AB 1234" required>
                            @error('vehicle_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">License Number</label>
                            <input type="text" name="license_number" class="form-control border-light-subtle bg-light bg-opacity-50 @error('license_number') is-invalid @enderror" value="{{ old('license_number') }}" placeholder="Enter driving license number" required>
                            @error('license_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" name="city" class="form-control border-light-subtle bg-light bg-opacity-50 @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="Enter city" required>
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-brand w-100 py-3 fw-bold">Save Driver Details</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
