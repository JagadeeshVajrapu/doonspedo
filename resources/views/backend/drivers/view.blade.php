@extends('layouts.admin')

@section('title', 'Driver Details')
@section('page_title', 'Driver Details & Management')

@section('content')
@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="row g-4">
    <!-- Left Column: Driver Info & Management -->
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card shadow-sm mb-4 border-0 rounded-4">
            <div class="card-body text-center p-4">
                <div class="mb-3 position-relative d-inline-block">
                    @if($driver->profile_image)
                        <img src="{{ media_url($driver->profile_image) }}" class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover;" alt="Profile">
                    @else
                        <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 120px; height: 120px;">
                            <i class="bi bi-person text-secondary display-4"></i>
                        </div>
                    @endif
                    
                    <span class="position-absolute bottom-0 end-0 p-2 bg-white border border-light rounded-circle shadow-sm" style="transform: translate(25%, 25%);">
                        @if($driver->status == 'approved')
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        @elseif($driver->status == 'pending')
                            <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                        @endif
                    </span>

                    <!-- Overlapping edit badge -->
                    <button class="btn btn-sm btn-dark position-absolute bottom-0 start-0 rounded-circle p-2 border border-2 border-white cursor-pointer shadow active-scale" 
                            style="transform: translate(-10%, 10%);" 
                            title="Update Profile Photo" 
                            data-bs-toggle="modal" 
                            data-bs-target="#photoUploadModal">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                </div>
                
                <h4 class="fw-bold mb-1">{{ $driver->name }}</h4>
                <p class="text-muted mb-2">{{ $driver->email }}</p>
                <div class="mb-3">
                    <button class="btn btn-xs btn-outline-secondary rounded-pill px-3 py-1 fw-bold fs-7 shadow-sm" data-bs-toggle="modal" data-bs-target="#photoUploadModal">
                        <i class="bi bi-camera me-1 text-primary"></i> Add/Change Photo
                    </button>
                </div>
                
                <!-- Status & Action Buttons -->
                <div class="d-flex justify-content-center gap-2 mb-4">
                    @if($driver->status == 'pending')
                        <a href="{{ route('admin.drivers.approve', $driver->id) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold"><i class="bi bi-check-lg me-1"></i> Approve KYC</a>
                        <a href="{{ route('admin.drivers.reject', $driver->id) }}" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold"><i class="bi bi-x-lg me-1"></i> Reject KYC</a>
                    @elseif($driver->status == 'approved')
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-4 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Verified & Approved</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-4 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i> KYC Rejected</span>
                    @endif
                </div>

                <!-- Driver ID Card -->
                <div class="driver-id-card position-relative overflow-hidden text-center rounded-4 mb-4 shadow border-0" 
                     style="background: #ffffff; width: 100%; max-width: 320px; margin: 0 auto;">
                    
                    <!-- ID Header (Theme Color) -->
                    <div class="position-relative pt-4 pb-5" style="background: linear-gradient(135deg, var(--admin-primary), #a8b51d);">
                        <!-- Abstract shapes -->
                        <div class="position-absolute" style="top: -10px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%;"></div>
                        <div class="position-absolute" style="bottom: -10px; left: -10px; width: 50px; height: 50px; background: rgba(255,255,255,0.15); border-radius: 50%;"></div>
                        
                        <div class="position-relative z-1 mb-2">
                            @if(!empty($sys_settings['app_logo']))
                                <img src="{{ asset($sys_settings['app_logo']) }}" alt="Logo" style="max-height: 45px; width: auto; object-fit: contain;">
                            @else
                                <span class="fw-bold fs-4 text-dark">{{ $sys_settings['app_name'] ?? 'Doonspedo' }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Photo (Overlapping header) -->
                    <div class="position-relative" style="margin-top: -55px; z-index: 2;">
                        <div class="d-inline-block p-1 bg-white rounded-circle shadow-sm">
                            @if($driver->profile_image)
                                <img src="{{ media_url($driver->profile_image) }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--admin-primary);" alt="Photo">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 100px; height: 100px; border: 3px solid var(--admin-primary);">
                                    <i class="bi bi-person text-secondary" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- ID Body: Details -->
                    <div class="p-4 pt-3 pb-4">
                        <h5 class="fw-bold text-dark mb-0 text-uppercase" style="letter-spacing: -0.5px;">{{ $driver->name }}</h5>
                        <p class="text-muted small mb-3 fw-bold">CAPTAIN</p>
                        
                        <!-- Fake Barcode -->
                        <div class="mb-3">
                            <i class="bi bi-upc" style="font-size: 2.5rem; color: #333; line-height: 1;"></i>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem; letter-spacing: 3px;">EMP-{{ 1000 + $driver->id }}</div>
                        </div>
                        
                        <div class="bg-light rounded-3 p-2 text-start small border">
                            <div class="d-flex justify-content-between mb-1 pb-1 border-bottom border-secondary border-opacity-10">
                                <span class="text-muted fw-bold">LICENSE:</span>
                                <span class="text-dark fw-bold">{{ $driver->license_number }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 pb-1 border-bottom border-secondary border-opacity-10">
                                <span class="text-muted fw-bold">VEHICLE:</span>
                                <span class="text-dark fw-bold">{{ strtoupper($driver->vehicle_number) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fw-bold">CITY:</span>
                                <span class="text-dark fw-bold">{{ strtoupper($driver->city) }}</span>
                            </div>
                        </div>
                        
                        @if($driver->status == 'approved')
                            <div class="mt-3 fs-6 d-inline-block bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill border border-success border-opacity-25">
                                <i class="bi bi-patch-check-fill me-1"></i> VERIFIED
                            </div>
                        @else
                            <div class="mt-3 fs-6 d-inline-block bg-danger bg-opacity-10 text-danger fw-bold px-3 py-1 rounded-pill border border-danger border-opacity-25">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> UNVERIFIED
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Driver ID Card (Back Side) -->
                <div class="driver-id-card-back position-relative overflow-hidden text-center rounded-4 mb-4 shadow border-0" 
                     style="background: #ffffff; width: 100%; max-width: 320px; margin: 0 auto; border-top: 5px solid var(--admin-primary) !important;">
                    
                    <div class="p-4 pt-4 pb-3">
                        <div class="mb-3">
                            @if(!empty($sys_settings['app_logo']))
                                <img src="{{ asset($sys_settings['app_logo']) }}" alt="Logo" style="max-height: 40px; width: auto; object-fit: contain;">
                            @else
                                <span class="fw-bold fs-5 text-dark">{{ $sys_settings['app_name'] ?? 'Doonspedo' }}</span>
                            @endif
                        </div>
                        
                        <p class="text-muted small fw-bold mb-3" style="font-size: 0.75rem;">
                            This card is the property of {{ $sys_settings['app_name'] ?? 'Doonspedo' }}.
                            <br>If found, please return to the address below.
                        </p>
                        
                        <div class="bg-light rounded-3 p-3 text-start small border mb-3">
                            <h6 class="fw-bold text-dark text-center border-bottom pb-2 mb-2">CONTACT INFO</h6>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-envelope-fill text-brand me-2"></i>
                                <span class="text-dark fw-bold">{{ $sys_settings['support_email'] ?? 'support@doonspedo.com' }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-telephone-fill text-brand me-2"></i>
                                <span class="text-dark fw-bold">{{ $sys_settings['contact_phone'] ?? '+1 800-456-7890' }}</span>
                            </div>
                        </div>

                        <!-- Fake Magnetic Stripe / Barcode for back -->
                        <div class="bg-dark w-100 mb-3" style="height: 35px; margin-left: -24px; width: calc(100% + 48px) !important;"></div>
                        
                        <div class="text-muted small" style="font-size: 0.70rem;">
                            Terms and conditions apply. Not transferable.
                        </div>
                    </div>
                </div>

                <hr class="text-muted opacity-25">
                
                <!-- Block / Unblock Settings -->
                <div class="bg-light rounded-3 p-3 text-start">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock text-brand me-2"></i> Account Status</h6>
                    <form action="{{ route('admin.drivers.block', $driver->id) }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold {{ $driver->is_blocked ? 'text-danger' : 'text-success' }}">
                                    {{ $driver->is_blocked ? 'Account Blocked' : 'Active Account' }}
                                </span>
                                <div class="small text-muted">{{ $driver->is_blocked ? 'Driver cannot access the platform' : 'Driver currently operates normally' }}</div>
                            </div>
                            <button type="submit" class="btn btn-sm {{ $driver->is_blocked ? 'btn-success' : 'btn-danger' }} rounded-pill px-3">
                                {{ $driver->is_blocked ? 'Unblock' : 'Block' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Commission Settings -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4"><i class="bi bi-percent text-primary me-2"></i> Driver Commission Settings</h6>
                <form action="{{ route('admin.drivers.commission.update', $driver->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Platform Fee / Commission Rate</label>
                        <div class="input-group">
                            <input type="number" step="0.5" min="0" max="100" class="form-control" name="commission_rate" value="{{ $driver->commission_rate ?? ($sys_settings['base_commission'] ?? 15.0) }}" required>
                            <span class="input-group-text bg-light">%</span>
                        </div>
                        <div class="form-text">Percentage deducted per ride for this specific driver.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Update Commission</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Documents and Details -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4 border-0 rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold"><i class="bi bi-info-square text-brand me-2"></i> Registration Details</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <label class="text-muted small fw-bold">Mobile Number</label>
                        <div class="fw-bold fs-6">{{ $driver->mobile }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-bold">Operating City</label>
                        <div class="fw-bold fs-6">{{ $driver->city }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-bold">Vehicle Type & Reg No.</label>
                        <div class="fw-bold fs-6">{{ ucfirst($driver->vehicle_type) }} <span class="badge bg-secondary ms-1">{{ $driver->vehicle_number }}</span></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-bold">License Number</label>
                        <div class="fw-bold fs-6">{{ $driver->license_number }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Verification -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-check text-success me-2"></i> Document Verification</h5>
            </div>
            <div class="card-body p-4 pt-0">
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
                            <div class="card h-100 border rounded-4 bg-light">
                                <div class="card-header bg-transparent border-0 py-3 pb-0">
                                    <h6 class="fw-bold text-dark text-center mb-0">
                                        <i class="bi {{ $doc['icon'] }} me-1 text-primary"></i> {{ $doc['label'] }}
                                    </h6>
                                </div>
                                <div class="card-body text-center p-3 d-flex flex-column justify-content-center" style="min-height: 250px;">
                                    @if($driver->{$doc['field']})
                                        <div class="doc-preview-container position-relative mb-2">
                                            <a href="{{ media_url($driver->{$doc['field']}) }}" target="_blank">
                                                <img src="{{ media_url($driver->{$doc['field']}) }}" class="img-fluid rounded shadow-sm doc-img-modal" alt="{{ $doc['label'] }}">
                                            </a>
                                        </div>
                                        <a href="{{ media_url($driver->{$doc['field']}) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mt-auto">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> View Original
                                        </a>
                                    @else
                                        <div class="py-4 mt-auto mb-auto">
                                            <i class="bi bi-file-earmark-x text-secondary opacity-50 display-3"></i>
                                            <p class="text-muted small mt-3 fw-bold">Missing Document</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Vehicle Verification & Documents -->
        @if(isset($driver->vehicles) && $driver->vehicles->count() > 0)
            @foreach($driver->vehicles as $vehicle)
                <div class="card shadow-sm border-0 rounded-4 mt-4 bg-white">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-car-front-fill text-brand me-2"></i> Vehicle: {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->number_plate }})</h5>
                        <span class="badge {{ $vehicle->verification_status == 'approved' ? 'bg-success' : ($vehicle->verification_status == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }} rounded-pill px-3">
                            {{ ucfirst($vehicle->verification_status) }}
                        </span>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-4 mb-4">
                            <div class="col-sm-3">
                                <label class="text-muted small fw-bold">Color</label>
                                <div class="fw-bold">{{ $vehicle->color }}</div>
                            </div>
                            <div class="col-sm-3">
                                <label class="text-muted small fw-bold">Year</label>
                                <div class="fw-bold">{{ $vehicle->year }}</div>
                            </div>
                            <div class="col-sm-3">
                                <label class="text-muted small fw-bold">Category</label>
                                <div class="fw-bold">{{ $vehicle->category->name ?? 'Default' }}</div>
                            </div>
                            <div class="col-sm-3">
                                <label class="text-muted small fw-bold">Status</label>
                                <div class="fw-bold"><span class="badge {{ $vehicle->status == 'active' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-2">{{ strtoupper($vehicle->status) }}</span></div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-check text-success me-1"></i> Vehicle Documents & Photos</h6>
                        <div class="row g-4">
                            @foreach($vehicle->documents as $vdoc)
                                <div class="col-lg-4">
                                    <div class="card h-100 border rounded-4 bg-light">
                                        <div class="card-header bg-transparent border-0 py-3 pb-0">
                                            <h6 class="fw-bold text-dark text-center mb-0">
                                                {{ $vdoc->document_name }}
                                            </h6>
                                        </div>
                                        <div class="card-body text-center p-3 d-flex flex-column justify-content-center" style="min-height: 200px;">
                                            @if(Str::endsWith($vdoc->document_path, '.pdf'))
                                                <div class="py-4 mt-auto mb-auto">
                                                    <i class="bi bi-file-earmark-pdf-fill text-danger display-4"></i>
                                                    <p class="text-dark small mt-2 fw-bold text-truncate" style="max-width: 150px;">{{ basename($vdoc->document_path) }}</p>
                                                </div>
                                            @else
                                                <div class="doc-preview-container position-relative mb-2">
                                                    <a href="{{ media_url($vdoc->document_path) }}" target="_blank">
                                                        <img src="{{ media_url($vdoc->document_path) }}" class="img-fluid rounded shadow-sm doc-img-modal" alt="{{ $vdoc->document_name }}">
                                                    </a>
                                                </div>
                                            @endif
                                            <a href="{{ media_url($vdoc->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mt-auto">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> View Document
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        
    </div>
</div>

<style>
.doc-img-modal {
    max-height: 180px;
    width: auto;
    object-fit: contain;
    border-radius: 8px;
    transition: transform 0.3s;
}
.doc-img-modal:hover {
    transform: scale(1.03);
}

.border-dashed {
    border: 2px dashed #dee2e6;
    border-radius: 1rem;
    transition: all 0.25s ease-in-out;
}
.border-dashed:hover {
    border-color: #cddc29 !important;
    background-color: rgba(205, 220, 41, 0.05);
}
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
.active-scale:active {
    transform: scale(0.97);
}
</style>

<!-- Photo Upload & Webcam Snapping Modal -->
<div class="modal fade" id="photoUploadModal" tabindex="-1" aria-labelledby="photoUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark mb-0" id="photoUploadModalLabel">Update Driver Photo</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close" id="close-photo-modal-btn"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <!-- Nav Tabs for Upload File vs Camera Snap -->
                <ul class="nav nav-pills justify-content-center mb-4 gap-2" id="photoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4 fw-bold shadow-sm" id="upload-tab" data-bs-toggle="pill" data-bs-target="#upload-pane" type="button" role="tab" aria-controls="upload-pane" aria-selected="true">
                            <i class="bi bi-upload me-1"></i> Upload File
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 fw-bold shadow-sm" id="camera-tab" data-bs-toggle="pill" data-bs-target="#camera-pane" type="button" role="tab" aria-controls="camera-pane" aria-selected="false" onclick="initWebcam()">
                            <i class="bi bi-camera-video me-1"></i> Snap Photo
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="photoTabContent">
                    <!-- Upload File Pane -->
                    <div class="tab-pane fade show active" id="upload-pane" role="tabpanel" aria-labelledby="upload-tab">
                        <form action="{{ route('admin.drivers.photo.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="border border-dashed rounded-4 p-5 mb-4 text-center cursor-pointer position-relative" onclick="document.getElementById('profile_image_file').click()">
                                <i class="bi bi-cloud-arrow-up text-brand display-4 mb-2"></i>
                                <h6 class="fw-bold mb-1">Click to select photo</h6>
                                <p class="text-muted small mb-0">Supported formats: JPG, PNG, WEBP (Max 2MB)</p>
                                <input type="file" name="profile_image" id="profile_image_file" class="d-none" accept="image/*" onchange="previewSelectedFile(this)">
                            </div>
                            <!-- Image Preview Area -->
                            <div id="file-preview-area" class="d-none mb-4 text-center">
                                <img id="selected-file-img" class="rounded-4 shadow-sm border border-light" style="max-height: 150px; width: auto; object-fit: contain;">
                            </div>
                            <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-bold shadow-sm active-scale">
                                SAVE PHOTO
                            </button>
                        </form>
                    </div>

                    <!-- Camera Snap Pane -->
                    <div class="tab-pane fade" id="camera-pane" role="tabpanel" aria-labelledby="camera-tab">
                        <form action="{{ route('admin.drivers.photo.update', $driver->id) }}" method="POST" id="camera-snap-form">
                            @csrf
                            <input type="hidden" name="profile_image_base64" id="profile_image_base64">
                            
                            <!-- Webcam Stream / Preview Frame -->
                            <div class="position-relative overflow-hidden bg-dark rounded-4 mb-4 shadow" style="min-height: 240px; height: 280px; width: 100%;">
                                <video id="webcam-video" class="w-100 h-100 rounded-4" style="object-fit: cover; transform: scaleX(-1);" autoplay playsinline></video>
                                <canvas id="webcam-canvas" class="d-none"></canvas>
                                
                                <!-- Capture Preview Overlay -->
                                <img id="captured-preview-img" class="position-absolute top-0 start-0 w-100 h-100 rounded-4 d-none" style="object-fit: cover; transform: scaleX(-1);">
                            </div>

                            <!-- Camera Controls -->
                            <div class="d-flex justify-content-center gap-3 mb-4">
                                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow active-scale" id="btn-snap" onclick="takeSnapshot()">
                                    <i class="bi bi-camera-fill me-1"></i> Capture Photo
                                </button>
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold border shadow active-scale d-none" id="btn-retake" onclick="retakeSnapshot()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Retake
                                </button>
                            </div>

                            <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-bold shadow-sm active-scale d-none" id="btn-save-snap">
                                SAVE CAPTURED PHOTO
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let stream = null;

async function initWebcam() {
    const video = document.getElementById('webcam-video');
    const canvas = document.getElementById('webcam-canvas');
    const preview = document.getElementById('captured-preview-img');
    const btnSnap = document.getElementById('btn-snap');
    const btnRetake = document.getElementById('btn-retake');
    const btnSave = document.getElementById('btn-save-snap');
    
    // Reset state
    preview.classList.add('d-none');
    video.classList.remove('d-none');
    btnSnap.classList.remove('d-none');
    btnRetake.classList.add('d-none');
    btnSave.classList.add('d-none');

    try {
        if (stream) {
            stopWebcam();
        }
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "user" },
            audio: false
        });
        video.srcObject = stream;
    } catch (err) {
        console.error("Webcam initialization failed: ", err);
        alert("Unable to access camera. Please check camera permissions or upload a file instead.");
    }
}

function stopWebcam() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

function takeSnapshot() {
    const video = document.getElementById('webcam-video');
    const canvas = document.getElementById('webcam-canvas');
    const preview = document.getElementById('captured-preview-img');
    const btnSnap = document.getElementById('btn-snap');
    const btnRetake = document.getElementById('btn-retake');
    const btnSave = document.getElementById('btn-save-snap');
    const inputBase64 = document.getElementById('profile_image_base64');

    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    const dataURL = canvas.toDataURL('image/jpeg');
    inputBase64.value = dataURL;

    preview.src = dataURL;
    preview.classList.remove('d-none');
    video.classList.add('d-none');
    
    btnSnap.classList.add('d-none');
    btnRetake.classList.remove('d-none');
    btnSave.classList.remove('d-none');
}

function retakeSnapshot() {
    initWebcam();
}

function previewSelectedFile(input) {
    const previewArea = document.getElementById('file-preview-area');
    const previewImg = document.getElementById('selected-file-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewArea.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Stop camera when modal is closed
document.addEventListener('DOMContentLoaded', function() {
    const photoModal = document.getElementById('photoUploadModal');
    if (photoModal) {
        photoModal.addEventListener('hidden.bs.modal', function () {
            stopWebcam();
        });
    }
});
</script>
@endsection
