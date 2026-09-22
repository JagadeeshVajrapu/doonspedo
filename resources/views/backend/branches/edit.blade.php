@extends('layouts.admin')

@section('title', 'Edit Branch')
@section('page_title', 'Update Branch')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.branches.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to Branches
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="admin-card">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold mb-0">Edit Branch Details</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.branches.update', $branch->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Branch Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ $branch->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Branch Login ID *</label>
                            <input type="text" name="login_id" class="form-control" value="{{ $branch->login_id }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">New Password (Optional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ $branch->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ $branch->email }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Full Address</label>
                            <textarea name="address" class="form-control" rows="3">{{ $branch->address }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Google Maps Embed Link (Optional)</label>
                            <input type="text" name="map_link" class="form-control" value="{{ $branch->map_link }}">
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-brand rounded-pill px-5 fw-bold shadow-sm py-2">
                                <i class="bi bi-check2-circle me-1"></i> Update Branch
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
