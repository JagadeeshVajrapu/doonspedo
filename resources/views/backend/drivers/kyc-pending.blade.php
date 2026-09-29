@extends('layouts.admin')

@section('title', 'Pending KYC Requests')
@section('page_title', 'Pending KYC')

@section('content')
@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">KYC approval requests</h2>
        @include('partials.ui.status-badge', ['label' => 'Pending', 'variant' => 'warning'])
    </div>
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th class="text-end px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($drivers) && count($drivers) > 0)
                        @foreach($drivers as $driver)
                    <tr>
                        <td class="px-4">#{{ 1000 + $driver->id }}</td>
                        <td>{{ $driver->name }}</td>
                        <td>{{ $driver->mobile }}</td>
                        <td>{{ $driver->created_at->format('d M Y') }}</td>
                        <td><span class="badge bg-warning text-dark rounded-pill">Pending</span></td>
                        <td class="text-end px-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.drivers.view', $driver->id) }}" class="btn btn-sm btn-outline-primary px-3 me-2 rounded-pill">
                                    <i class="bi bi-eye me-1"></i> View Docs
                                </a>
                                <a href="{{ route('admin.drivers.approve', $driver->id) }}" class="btn btn-sm btn-success px-3 me-2 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Approve
                                </a>
                                <a href="{{ route('admin.drivers.reject', $driver->id) }}" class="btn btn-sm btn-outline-danger px-3 rounded-pill" onclick="return confirm('Are you sure you want to reject this driver?')">
                                    <i class="bi bi-x-circle me-1"></i> Reject
                                </a>
                            </div>
                        </td>
                    </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No pending KYC requests.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals outside the table for reliability -->
@if(isset($drivers) && count($drivers) > 0)
@foreach($drivers as $driver)
<div class="modal fade" id="docModal_{{ $driver->id }}" tabindex="-1" aria-labelledby="docModalLabel_{{ $driver->id }}" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                <h5 class="modal-title fw-bold" id="docModalLabel_{{ $driver->id }}">
                    <i class="bi bi-person-badge text-brand me-2"></i> Documents: {{ $driver->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    @php
                        $docs = [
                            ['label' => 'Profile Photo', 'field' => 'profile_image', 'icon' => 'bi-person-circle'],
                            ['label' => 'Driving License', 'field' => 'license_image', 'icon' => 'bi-card-text'],
                            ['label' => 'Aadhaar Card', 'field' => 'aadhaar_image', 'icon' => 'bi-person-vcard'],
                        ];
                    @endphp

                    @foreach($docs as $doc)
                        <div class="col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4">
                                <div class="card-header bg-white border-0 py-3">
                                    <h6 class="fw-bold text-dark mb-0 d-flex justify-content-between align-items-center">
                                        <span><i class="bi {{ $doc['icon'] }} me-2 text-primary"></i>{{ $doc['label'] }}</span>
                                        @if($driver->{$doc['field']})
                                            <a href="{{ media_url($driver->{$doc['field']}) }}" target="_blank" class="btn btn-sm btn-link p-0 text-primary" title="Open in new tab">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @endif
                                    </h6>
                                </div>
                                <div class="card-body bg-white rounded-bottom-4 text-center p-3 d-flex flex-column justify-content-center" style="min-height: 300px;">
                                    @if($driver->{$doc['field']})
                                        <div class="doc-preview-container position-relative">
                                            <img src="{{ media_url($driver->{$doc['field']}) }}" class="img-fluid rounded shadow-sm doc-img-modal" alt="{{ $doc['label'] }}">
                                        </div>
                                    @else
                                        <div class="py-5">
                                            <i class="bi bi-file-earmark-x text-light display-1"></i>
                                            <p class="text-muted small mt-3">Document not uploaded yet</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-white border-0 py-3 px-4">
                <div class="me-auto text-muted small">
                    <i class="bi bi-info-circle me-1"></i> Review documents carefully before approval.
                </div>
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('admin.drivers.approve', $driver->id) }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Approve This Driver
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

<style>
.doc-img-modal {
    max-height: 400px;
    width: auto;
    object-fit: contain;
    border-radius: 12px;
}
.btn-brand-outline {
    border: 2px solid #cddc29;
    color: #cddc29;
}
.btn-brand-outline:hover {
    background: #cddc29;
    color: #000;
}
.text-brand { color: #cddc29; }
</style>
@endsection
