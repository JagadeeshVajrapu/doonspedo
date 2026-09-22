@extends('layouts.admin')

@section('title', 'Rental Packages')
@section('page_title', 'Rental Packages Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-brand me-2"></i> Manage Rental Packages</h5>
    <button class="btn btn-brand btn-sm px-3 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Rental Package
    </button>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Package Name</th>
                        <th class="py-3">Duration</th>
                        <th class="py-3">Max Distance</th>
                        <th class="py-3">Base Price</th>
                        <th class="py-3">Vehicle Type</th>
                        <th class="text-end px-4 py-3">Status</th>
                        <th class="text-end px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 fw-bold">1 Hour Special</td>
                        <td><span class="badge bg-secondary">1 Hour</span></td>
                        <td>10 KM</td>
                        <td class="fw-bold text-success">{{ $default_currency->symbol ?? '₹' }}15.00</td>
                        <td><i class="bi bi-car-front text-muted me-1"></i> All Types</td>
                        <td class="text-end px-4">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </td>
                        <td class="text-end px-4">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" aria-label="Edit package" title="Edit"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 fw-bold">Half Day (4 Hrs)</td>
                        <td><span class="badge bg-secondary">4 Hours</span></td>
                        <td>40 KM</td>
                        <td class="fw-bold text-success">{{ $default_currency->symbol ?? '₹' }}40.00</td>
                        <td><i class="bi bi-car-front-fill text-muted me-1"></i> Premium Only</td>
                        <td class="text-end px-4">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </td>
                        <td class="text-end px-4">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" aria-label="Edit package" title="Edit"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 fw-bold">Full Day (8 Hrs)</td>
                        <td><span class="badge bg-secondary">8 Hours</span></td>
                        <td>80 KM</td>
                        <td class="fw-bold text-success">{{ $default_currency->symbol ?? '₹' }}75.00</td>
                        <td><i class="bi bi-truck-front text-muted me-1"></i> SUVs</td>
                        <td class="text-end px-4">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td class="text-end px-4">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" aria-label="Edit package" title="Edit"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
