@extends('layouts.admin')

@section('title', 'KYC Requirements')
@section('page_title', 'KYC Document Requirements')

@section('content')
<div class="row">
    <div class="col-md-8">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="admin-card">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Configured Documents</h6>
            </div>
            <div class="card-body p-0">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle mb-0 admin-responsive-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Document Name</th>
                                <th>Type</th>
                                <th>Required?</th>
                                <th>Active?</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($requirements) && count($requirements) > 0)
                                @foreach($requirements as $req)
                            <tr>
                                <td class="px-4 fw-bold text-dark">{{ $req->document_name }}</td>
                                <td>
                                    @if($req->document_type == 'image')
                                        <span class="badge bg-primary rounded-pill"><i class="bi bi-image me-1"></i> Image</span>
                                    @elseif($req->document_type == 'pdf')
                                        <span class="badge bg-info text-dark rounded-pill"><i class="bi bi-file-pdf me-1"></i> PDF</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-type me-1"></i> Text</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->is_required)
                                        <span class="badge bg-danger rounded-pill">Yes</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">Optional</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->is_active)
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-dark rounded-pill">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $req->id }}">Edit</button>
                                    
                                    <form action="{{ route('admin.settings.kyc.destroy', $req->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this document requirement?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No KYC documents configured.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modals outside for reliability -->
        @if(isset($requirements) && count($requirements) > 0)
        @foreach($requirements as $req)
        <div class="modal fade" id="editModal{{ $req->id }}" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-light border-0 py-3">
                        <h5 class="modal-title fw-bold">Edit Requirement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.settings.kyc.update', $req->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Document Name</label>
                                <input type="text" name="document_name" class="form-control" value="{{ $req->document_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Document Type</label>
                                <select name="document_type" class="form-select bg-light" required>
                                    <option value="image" {{ $req->document_type == 'image' ? 'selected' : '' }}>Image (JPG/PNG)</option>
                                    <option value="pdf" {{ $req->document_type == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                                    <option value="text" {{ $req->document_type == 'text' ? 'selected' : '' }}>Text Input Only</option>
                                </select>
                            </div>
                            <div class="form-check form-switch mb-2 pt-2">
                                <input class="form-check-input" type="checkbox" name="is_required" id="isReq{{ $req->id }}" {{ $req->is_required ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isReq{{ $req->id }}">Required for Approval?</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isAct{{ $req->id }}" {{ $req->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isAct{{ $req->id }}">Active (Visible to driver)?</label>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-brand rounded-pill px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
    
    <div class="col-md-4">
        <div class="admin-card">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold">Add New Document</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.settings.kyc.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Document Title</label>
                        <input type="text" name="document_name" class="form-control bg-light @error('document_name') is-invalid @enderror" placeholder="e.g. Police Clearance" required>
                        @error('document_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Document Type</label>
                        <select name="document_type" class="form-select bg-light @error('document_type') is-invalid @enderror" required>
                            <option value="image" selected>Image (JPG/PNG)</option>
                            <option value="pdf">PDF Document</option>
                            <option value="text">Text Input Only</option>
                        </select>
                        @error('document_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_required" id="newReq" checked>
                        <label class="form-check-label" for="newReq">Required for Approval?</label>
                        <div class="form-text mt-0">If unchecked, driver can submit KYC without this.</div>
                    </div>
                    
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="newAct" checked>
                        <label class="form-check-label" for="newAct">Currently Active?</label>
                    </div>
                    
                    <button type="submit" class="btn btn-brand w-100 fw-bold">Add Requirement</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
