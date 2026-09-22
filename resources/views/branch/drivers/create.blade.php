@extends('layouts.admin')

@section('title', 'Add New Driver')
@section('page_title', 'Register New Driver')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="admin-card">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold mb-0">Driver Details</h5>
                <p class="text-muted small mb-0">This driver will be automatically assigned to your branch.</p>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('branch.drivers.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Full Name *</label>
                            <input type="text" name="name" class="form-control py-2 shadow-sm border-0 bg-light" placeholder="Enter driver full name" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Mobile Number *</label>
                            <input type="text" name="mobile" class="form-control py-2 shadow-sm border-0 bg-light" placeholder="+91 00000 00000" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehicle Number *</label>
                            <input type="text" name="vehicle_number" class="form-control py-2 shadow-sm border-0 bg-light" placeholder="e.g. DL 01 AB 1234" required>
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-brand w-100 py-3 fw-bold rounded-pill shadow">
                                <i class="bi bi-person-plus me-2"></i> Register Driver
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('branch.drivers.index') }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Back to Drivers List</a>
        </div>
    </div>
</div>
@endsection
