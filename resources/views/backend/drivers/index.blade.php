@extends('layouts.admin')

@section('title', 'All Drivers')
@section('page_title', 'All Drivers')

@section('content')
@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="admin-card mb-4">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Driver list</h2>
        <a href="{{ route('admin.drivers.create') }}" class="btn btn-brand btn-sm px-3 rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Add new driver
        </a>
    </div>
    <div class="admin-table-wrap">
        <table class="table table-hover align-middle mb-0 admin-responsive-table">
            <thead>
                <tr>
                    <th class="px-4">ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Vehicle</th>
                    <th>KYC status</th>
                    <th class="text-end px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($drivers) && count($drivers) > 0)
                    @foreach($drivers as $driver)
                <tr>
                    <td class="px-4">#{{ 1000 + $driver->id }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 p-2 rounded-circle me-2">
                                <i class="bi bi-person"></i>
                            </div>
                            <span class="fw-semibold">{{ $driver->name }}</span>
                        </div>
                    </td>
                    <td>{{ $driver->mobile }}</td>
                    <td>{{ ucfirst($driver->vehicle_type) }} ({{ $driver->vehicle_number }})</td>
                    <td>
                        @if($driver->status == 'approved')
                            @include('partials.ui.status-badge', ['label' => 'Approved', 'variant' => 'success'])
                        @elseif($driver->status == 'pending')
                            @include('partials.ui.status-badge', ['label' => 'Pending', 'variant' => 'warning'])
                        @else
                            @include('partials.ui.status-badge', ['label' => 'Rejected', 'variant' => 'danger'])
                        @endif
                    </td>
                    <td class="text-end px-4">
                        <a href="{{ route('admin.drivers.view', $driver->id) }}" class="btn btn-sm btn-outline-primary px-2 rounded-pill shadow-sm" title="View Details" aria-label="View driver">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.drivers.edit', $driver->id) }}" class="btn btn-sm btn-outline-secondary px-2 rounded-pill shadow-sm mx-1" title="Edit Profile" aria-label="Edit driver">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.drivers.destroy', $driver->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this driver?');" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 rounded-pill shadow-sm" title="Delete Profile" aria-label="Delete driver"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="6" class="p-4">
                        @include('partials.ui.empty-state', [
                            'title' => 'No drivers found',
                            'message' => 'Add a driver or wait for new registrations.',
                            'icon' => 'bi-person-badge',
                            'actionLabel' => 'Add driver',
                            'actionUrl' => route('admin.drivers.create'),
                        ])
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
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
                    @if(isset($driver->documents) && count($driver->documents) > 0)
                        @foreach($driver->documents as $doc)
                        <div class="col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4">
                                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                        {{ $doc->kycRequirement->document_name }}
                                    </h6>
                                    <div>
                                        <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'rejected' ? 'danger' : 'warning') }} rounded-pill">
                                            {{ ucfirst($doc->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body bg-white text-center p-3 d-flex flex-column justify-content-center" style="min-height: 300px;">
                                    <div class="doc-preview-container mb-4">
                                        @if($doc->kycRequirement->document_type == 'image')
                                            <img src="{{ asset('storage/' . $doc->document_path) }}" class="img-fluid rounded shadow-sm doc-img-modal" alt="KYC Doc">
                                        @elseif($doc->kycRequirement->document_type == 'pdf')
                                            <div class="py-4 bg-light rounded-4">
                                                <i class="bi bi-file-earmark-pdf text-danger display-1"></i>
                                                <div class="mt-3">
                                                    <a href="{{ asset('storage/' . $doc->document_path) }}" target="_blank" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">
                                                       <i class="bi bi-eye me-1"></i> VIEW PDF
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-4 bg-light rounded-4 border border-warning-subtle text-start">
                                                <div class="small fw-bold text-muted mb-2"><i class="bi bi-quote me-1"></i> TEXT INPUT:</div>
                                                <div class="text-dark font-monospace" style="white-space: pre-wrap;">{{ $doc->document_path }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-center gap-2 mt-auto">
                                        <form action="{{ route('admin.drivers.documents.status', $doc->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" {{ $doc->status == 'approved' ? 'disabled' : '' }}>
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.drivers.documents.status', $doc->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3" {{ $doc->status == 'rejected' ? 'disabled' : '' }}>
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-file-earmark-x display-1 text-light"></i>
                            <p class="text-muted mt-3">No documents uploaded by driver yet.</p>
                        </div>
                    @endif
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
