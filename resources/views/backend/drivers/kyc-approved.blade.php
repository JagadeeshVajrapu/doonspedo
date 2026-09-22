@extends('layouts.admin')

@section('title', 'KYC Approved Drivers')
@section('page_title', 'KYC Approved')

@section('content')
<div class="admin-card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Verified Drivers</h5>
    </div>
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Verification Date</th>
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
                        <td>{{ $driver->updated_at->format('d M Y') }}</td>
                        <td><span class="badge bg-success rounded-pill">Verified</span></td>
                        <td class="text-end px-4">
                            <a href="{{ route('admin.drivers.view', $driver->id) }}" class="btn btn-sm btn-outline-primary px-2 rounded-pill" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No verified drivers found.</td>
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
                                            <a href="{{ asset('storage/' . $driver->{$doc['field']}) }}" target="_blank" class="btn btn-sm btn-link p-0 text-primary" title="Open in new tab">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @endif
                                    </h6>
                                </div>
                                <div class="card-body bg-white rounded-bottom-4 text-center p-3 d-flex flex-column justify-content-center" style="min-height: 300px;">
                                    @if($driver->{$doc['field']})
                                        <div class="doc-preview-container position-relative">
                                            <img src="{{ asset('storage/' . $driver->{$doc['field']}) }}" class="img-fluid rounded shadow-sm doc-img-modal" alt="{{ $doc['label'] }}">
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
            <div class="modal-footer bg-white border-0 py-3 px-4 text-end">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
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
.text-brand { color: #cddc29; }
</style>
@endsection
