@extends('layouts.driver')

@section('title', 'Add New Vehicle - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Add vehicle</h1>
        <p class="text-muted mb-0 small">Enter vehicle details and upload necessary documents.</p>
    </div>
    <a href="{{ route('driver.vehicles.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Back to list
    </a>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="drv-card overflow-hidden mb-4">
            <div class="py-3 px-4 border-bottom">
                <h2 class="h5 fw-bold mb-0">Vehicle details & documents</h2>
            </div>
            <div class="p-4">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
                        <ul class="mb-0 small fw-bold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('driver.vehicles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-4 mb-5 pb-4 border-bottom">
                        <div class="col-12">
                            <h3 class="h6 text-uppercase small text-muted fw-bold mb-3"><i class="bi bi-info-circle me-1 text-brand"></i> Basic specification</h3>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehicle Category</label>
                            <select name="vehicle_category_id" class="form-select rounded-3 p-3 bg-light border-0 shadow-none" required>
                                <option value="" disabled selected>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->capacity_seats }} Seats)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Brand / Make</label>
                            <input type="text" name="brand" class="form-control rounded-3 p-3 bg-light border-0 shadow-none" placeholder="e.g., Maruti Suzuki, Toyota" required>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Model Name</label>
                            <input type="text" name="model" class="form-control rounded-3 p-3 bg-light border-0 shadow-none" placeholder="e.g., Swift Dzire, Innova" required>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Registration Number</label>
                            <input type="text" name="number_plate" class="form-control rounded-3 p-3 bg-light border-0 shadow-none text-uppercase" placeholder="DL 01 AB 1234" required>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehicle Color</label>
                            <input type="text" name="color" class="form-control rounded-3 p-3 bg-light border-0 shadow-none" placeholder="White, Silver, Black" required>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Registration Year</label>
                            <input type="number" name="year" class="form-control rounded-3 p-3 bg-light border-0 shadow-none" placeholder="2024" required>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <h6 class="text-uppercase small text-muted fw-bold mb-3"><i class="bi bi-file-earmark-arrow-up me-1 text-brand"></i> Required Documents & Vehicle Photo</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="rc_book" class="d-block p-4 rounded-4 bg-light border-dashed text-center position-relative h-100" style="cursor: pointer; transition: all 0.3s ease;">
                                <i class="bi bi-card-checklist fs-1 text-brand mb-2 d-block"></i>
                                <h6 class="fw-bold mb-1">Vehicle Registration (RC)</h6>
                                <p class="extra-small text-muted mb-0">Upload clear photo of RC Book (PDF, JPG, PNG)</p>
                                <input type="file" name="rc_book" id="rc_book" class="d-none" accept="image/*,application/pdf" required onchange="showPreview(this, 'rc_preview')">
                                <div id="rc_preview" class="text-brand small fw-bold mt-2"></div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label for="insurance" class="d-block p-4 rounded-4 bg-light border-dashed text-center position-relative h-100" style="cursor: pointer; transition: all 0.3s ease;">
                                <i class="bi bi-shield-check fs-1 text-brand mb-2 d-block"></i>
                                <h6 class="fw-bold mb-1">Vehicle Insurance</h6>
                                <p class="extra-small text-muted mb-0">Upload valid insurance policy document (PDF, JPG, PNG)</p>
                                <input type="file" name="insurance" id="insurance" class="d-none" accept="image/*,application/pdf" required onchange="showPreview(this, 'ins_preview')">
                                <div id="ins_preview" class="text-brand small fw-bold mt-2"></div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label for="vehicle_image" class="d-block p-4 rounded-4 bg-light border-dashed text-center position-relative h-100" style="cursor: pointer; transition: all 0.3s ease;">
                                <i class="bi bi-camera-fill fs-1 text-brand mb-2 d-block"></i>
                                <h6 class="fw-bold mb-1">Vehicle Photo</h6>
                                <p class="extra-small text-muted mb-0">Upload or capture a clear photo of the vehicle (JPG, PNG)</p>
                                <input type="file" name="vehicle_image" id="vehicle_image" class="d-none" accept="image/*" capture="environment" required onchange="showPreview(this, 'veh_preview')">
                                <div id="veh_preview" class="text-brand small fw-bold mt-2"></div>
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-3 mt-5">
                        <button type="submit" class="btn btn-brand btn-lg rounded-pill fw-bold py-3 shadow active-scale">
                            SAVE VEHICLE & SUBMIT DOCS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.bg-brand {
    background-color: #cddc29 !important;
}
.text-brand {
    color: #cddc29 !important;
}
.btn-brand {
    background-color: #cddc29;
    color: #000;
}
.btn-brand:hover {
    background-color: #b9c825;
}
.border-dashed {
    border: 2px dashed #dee2e6;
}
.border-dashed:hover {
    background-color: rgba(205, 220, 41, 0.05) !important;
    border-color: #cddc29 !important;
}
.active-scale:active {
    transform: scale(0.98);
}
.extra-small {
    font-size: 0.75rem;
}
</style>

<script>
function showPreview(input, previewId) {
    const fileName = input.files[0] ? input.files[0].name : '';
    const nameDiv = document.getElementById(previewId);
    if (fileName) {
        nameDiv.textContent = 'Selected: ' + fileName;
        input.closest('.p-4').style.borderColor = '#cddc29';
        input.closest('.p-4').classList.remove('opacity-75');
    } else {
        nameDiv.textContent = '';
    }
}
</script>
@endsection
