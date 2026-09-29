@extends('layouts.driver')

@section('title', 'Complete KYC - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>KYC / Documents</h1>
        <p class="text-muted mb-0 small">Upload the required documents to start accepting rides.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="drv-card overflow-hidden mb-4">
            <div class="py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="h5 fw-bold mb-0">Identity & vehicle documents</h2>
                @include('partials.ui.status-badge', [
                    'label' => ucfirst($driver->status),
                    'variant' => $driver->status == 'approved' ? 'success' : ($driver->status == 'rejected' ? 'danger' : 'warning'),
                ])
            </div>
            
            <div class="p-4 pt-3">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('driver.kyc.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @foreach($requirements as $req)
                        @php
                            $existingDoc = $driver->documents->where('kyc_requirement_id', $req->id)->first();
                        @endphp
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase d-block mb-3">
                                {{ $req->document_name }} 
                                @if($req->is_required) <span class="text-danger">*</span> @endif
                            </label>
                            
                                @if($req->document_type == 'text')
                                    <div class="upload-container position-relative rounded-4 p-4 text-center border-dashed {{ $existingDoc ? 'bg-light border-brand opacity-100' : 'bg-light border-light opacity-75' }}" style="transition: all 0.3s ease;">
                                        <textarea name="doc_{{ $req->id }}" class="form-control bg-white border-0 rounded-3 shadow-none" rows="3" placeholder="Enter {{ $req->document_name }} here..." {{ $req->is_required && !$existingDoc ? 'required' : '' }}>{{ $existingDoc ? $existingDoc->document_path : '' }}</textarea>
                                    </div>
                                @else
                                    <div class="upload-container d-block position-relative rounded-4 p-4 text-center border-dashed {{ $existingDoc ? 'bg-light border-brand opacity-100' : 'bg-light border-light opacity-75' }}" style="transition: all 0.3s ease;">
                                        <div class="d-flex flex-column align-items-center">
                                            @if($existingDoc)
                                                <div class="mb-2" onclick="triggerFileInput('{{ $req->id }}', false)" style="cursor: pointer;">
                                                    @if($req->document_type == 'image')
                                                        <img src="{{ media_url($existingDoc->document_path) }}" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 120px; object-fit: contain;">
                                                    @else
                                                        <div class="bg-danger bg-opacity-10 p-4 rounded-circle mb-2">
                                                            <i class="bi bi-file-earmark-pdf-fill fs-1 text-danger"></i>
                                                        </div>
                                                        <div class="small fw-bold text-dark">PDF DOCUMENT</div>
                                                    @endif
                                                </div>
                                                <div class="mb-2">
                                                    @include('partials.ui.status-badge', [
                                                        'label' => ucfirst($existingDoc->status),
                                                        'variant' => $existingDoc->status == 'approved' ? 'success' : ($existingDoc->status == 'rejected' ? 'danger' : 'warning'),
                                                    ])
                                                </div>
                                                <p class="small text-muted mb-0">Click image to change, or</p>
                                                @if($req->document_type == 'image')
                                                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill mt-2" onclick="triggerFileInput('{{ $req->id }}', true)">
                                                        <i class="bi bi-camera me-1"></i> Take New Photo
                                                    </button>
                                                @endif
                                            @else
                                                @if($req->document_type == 'image')
                                                    <div class="d-flex gap-3 w-100 justify-content-center mb-3">
                                                        <button type="button" class="btn btn-light border shadow-sm rounded-4 p-3 flex-fill text-center" onclick="triggerFileInput('{{ $req->id }}', true)">
                                                            <i class="bi bi-camera fs-3 d-block mb-1 text-dark"></i>
                                                            <span class="small fw-bold">Camera</span>
                                                        </button>
                                                        <button type="button" class="btn btn-light border shadow-sm rounded-4 p-3 flex-fill text-center" onclick="triggerFileInput('{{ $req->id }}', false)">
                                                            <i class="bi bi-image fs-3 d-block mb-1 text-dark"></i>
                                                            <span class="small fw-bold">Gallery</span>
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="bg-brand bg-opacity-10 p-4 rounded-circle mb-3" style="cursor: pointer;" onclick="triggerFileInput('{{ $req->id }}', false)">
                                                        <i class="bi bi-file-earmark-pdf fs-2 text-brand"></i>
                                                    </div>
                                                    <p class="small fw-bold text-dark mb-1" style="cursor: pointer;" onclick="triggerFileInput('{{ $req->id }}', false)">Click to upload PDF</p>
                                                @endif
                                                <p class="text-muted extra-small mb-0">Supported: {{ $req->document_type == 'image' ? 'PNG, JPG, WEBP' : 'PDF (max 5MB)' }}</p>
                                            @endif
                                        </div>
                                        <input type="file" name="doc_{{ $req->id }}" id="doc_{{ $req->id }}" class="d-none" accept="{{ $req->document_type == 'image' ? 'image/*' : '.pdf' }}" {{ $req->is_required && !$existingDoc ? 'required' : '' }} onchange="updateFileName(this, '{{ $req->id }}')">
                                        <div id="file_name_{{ $req->id }}" class="text-brand small fw-bold mt-3 d-none"></div>
                                    </div>
                                @endif
                            @if($existingDoc && $existingDoc->status == 'rejected')
                                <div class="bg-danger bg-opacity-10 p-2 rounded-3 mt-2">
                                    <p class="text-danger small mb-0 fw-bold">
                                        <i class="bi bi-exclamation-circle me-1"></i> Rejected by admin. Please upload a clear copy.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="d-grid gap-3 mt-5">
                        <button type="submit" class="btn btn-brand btn-lg rounded-pill fw-bold py-3 shadow active-scale">
                            SUBMIT DOCUMENTS FOR VERIFICATION
                        </button>
                        <a href="{{ route('driver.dashboard') }}" class="btn btn-light btn-lg rounded-pill fw-bold py-3">BACK TO DASHBOARD</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-1 text-brand"></i> Guidelines</h6>
            <ul class="nav flex-column gap-3">
                <li class="d-flex align-items-start small text-muted">
                    <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                    Document should be clear and readable.
                </li>
                <li class="d-flex align-items-start small text-muted">
                    <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                    Expiry date must be visible and valid.
                </li>
                <li class="d-flex align-items-start small text-muted">
                    <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                    Full borders of the card/paper should be visible.
                </li>
                <li class="d-flex align-items-start small text-muted">
                    <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                    Maximum file size: Image - 2MB, PDF - 5MB.
                </li>
            </ul>
        </div>
        
        <div class="card border-0 shadow-sm rounded-4 bg-brand text-dark p-4">
            <h6 class="fw-bold mb-2">Why we need this?</h6>
            <p class="extra-small opacity-75 mb-0">Safety is our top priority. We verify documents to ensure all drivers are qualified and authorized to operate.</p>
        </div>
    </div>
</div>

<style>
.upload-container:hover {
    background-color: rgba(205, 220, 41, 0.05) !important;
    border-color: #cddc29 !important;
    opacity: 1 !important;
}
.border-dashed {
    border: 2px dashed #dee2e6;
}
.border-brand {
    border-color: #cddc29 !important;
}
.btn-brand {
    background-color: #cddc29;
    color: #000;
}
.btn-brand:hover {
    background-color: #b9c825;
}
.text-brand {
    color: #cddc29 !important;
}
.extra-small {
    font-size: 0.75rem;
}
</style>

<script>
function triggerFileInput(id, useCamera) {
    const input = document.getElementById('doc_' + id);
    if (useCamera) {
        input.setAttribute('capture', 'environment');
    } else {
        input.removeAttribute('capture');
    }
    input.click();
}

function updateFileName(input, id) {
    const fileName = input.files[0] ? input.files[0].name : '';
    const nameDiv = document.getElementById('file_name_' + id);
    if (fileName) {
        nameDiv.textContent = 'Selected: ' + fileName;
        nameDiv.classList.remove('d-none');
        input.closest('.upload-container').classList.add('border-brand');
    } else {
        nameDiv.classList.add('d-none');
    }
}
</script>
@endsection
