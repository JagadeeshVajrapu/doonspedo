@extends('layouts.admin')

@section('title', 'Promotional Banners')
@section('page_title', 'App & Web Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-images text-brand me-2"></i> Promotional Banners</h5>
    <button type="button" class="btn btn-brand rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createBannerModal">
        <i class="bi bi-plus-lg me-1"></i> Upload Banner
    </button>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="row g-4">
    @if(isset($banners) && count($banners) > 0)
        @foreach($banners as $banner)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative {{ !$banner->is_active ? 'opacity-75' : '' }}">
                <div class="position-absolute top-0 end-0 m-2">
                    @if($banner->is_active)
                        <span class="badge bg-success bg-opacity-75 shadow-sm rounded-pill px-3 py-1">Active</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-75 shadow-sm rounded-pill px-3 py-1">Hidden</span>
                    @endif
                </div>

                @if($banner->image_path)
                    <div class="bg-light w-100 border-bottom border-secondary border-opacity-10" style="height: 180px; background-image: url('{{ asset('storage/' . $banner->image_path) }}'); background-size: cover; background-position: center;">
                    </div>
                @else
                    <div class="bg-light w-100 d-flex align-items-center justify-content-center border-bottom border-secondary border-opacity-10" style="height: 180px; background-size: cover; background-position: center;">
                        <i class="bi bi-image text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                @endif
                
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="fw-bold text-dark mb-1">{{ $banner->title }}</h5>
                    <p class="small text-primary text-truncate mb-3"><i class="bi bi-link-45deg me-1"></i>{{ $banner->link ?? 'No Target URL' }}</p>
                    
                    <div class="d-flex align-items-center mb-4 text-muted small mt-auto">
                        <span><i class="bi bi-sort-numeric-down me-1"></i> Position: {{ $banner->position }}</span>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary w-50 rounded-pill btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#editBannerModal{{ $banner->id }}"><i class="bi bi-pencil me-1"></i> Edit</button>
                        <form action="{{ route('admin.cms.banners.destroy', $banner->id) }}" method="POST" class="w-50" onsubmit="return confirm('Delete this banner?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill btn-sm fw-bold"><i class="bi bi-trash me-1"></i> Delete</button>
                        </form>
                    </div>

                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="col-12 text-center py-5">
            <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
            </div>
            <h5 class="text-muted fw-bold">No Banners Currently Running</h5>
            <p class="text-muted small">Upload promotional banners here. These will reflect on the rider and driver dashboard headers.</p>
        </div>
    @endif
</div>

<div class="mt-4">
    {{ $banners->links() }}
</div>

<!-- Create Banner Modal -->
<div class="modal fade" id="createBannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-upload text-brand me-2"></i> Upload New Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.cms.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Banner Image *</label>
                        <input type="file" class="form-control" name="image" required>
                        <div class="form-text small">Recommended size: 1200x400px (JPEG, PNG, JPG)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Campaign Name / Internal Title *</label>
                        <input type="text" class="form-control" name="title" placeholder="e.g. Summer Promo 50% Off" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Target Link URL (Optional)</label>
                        <input type="url" class="form-control" name="link" placeholder="https://doonspedo.com/promo-details">
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Display Position</label>
                            <input type="number" class="form-control" name="position" value="1" min="1">
                        </div>
                        <div class="col-sm-6 d-flex align-items-end mb-2">
                            <div class="form-check form-switch fs-5 pb-1">
                                <input class="form-check-input" type="checkbox" name="is_active" id="activeBannerSwitch" checked value="1">
                                <label class="form-check-label fs-6 fw-bold ms-2 mt-1" for="activeBannerSwitch">Banner is Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4 fw-bold">Upload Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@if(isset($banners) && count($banners) > 0)
@foreach($banners as $banner)
<!-- Edit Banner Modal -->
<div class="modal fade" id="editBannerModal{{ $banner->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-start">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-brand me-2"></i> Edit Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.cms.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3 text-center p-3 border border-dashed rounded-3 bg-light">
                        @if($banner->image_path)
                            <img src="{{ asset('storage/'.$banner->image_path) }}" class="img-fluid rounded-3 mb-2" style="max-height: 100px;">
                        @endif
                        <p class="small text-muted mb-0">Upload new image to replace</p>
                        <input type="file" class="form-control mt-2" name="image">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Title *</label>
                        <input type="text" class="form-control" name="title" value="{{ $banner->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Link URL</label>
                        <input type="url" class="form-control" name="link" value="{{ $banner->link }}">
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-muted">Position</label>
                            <input type="number" class="form-control" name="position" value="{{ $banner->position }}" min="1">
                        </div>
                        <div class="col-sm-6 d-flex align-items-end mb-2">
                            <div class="form-check form-switch fs-5 pb-1">
                                <input class="form-check-input" type="checkbox" name="is_active" id="activeSwitch{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }} value="1">
                                <label class="form-check-label fs-6 fw-bold ms-2 mt-1" for="activeSwitch{{ $banner->id }}">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4 fw-bold">Update Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection
