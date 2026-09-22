@extends('layouts.admin')

@section('title', 'Add New Branch')
@section('page_title', 'Create Branch')

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
                <h5 class="fw-bold mb-0">Branch Details</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.branches.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Branch Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Main Office" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Branch Login ID *</label>
                            <input type="text" name="login_id" class="form-control" placeholder="e.g. branch01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Login Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 00000 00000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="branch@example.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Full Address</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="Enter branch address..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Google Maps Embed Link (Optional)</label>
                            <input type="text" name="map_link" class="form-control" placeholder="https://www.google.com/maps/embed?pb=...">
                            <div class="form-text">Paste the iframe src URL from Google Maps share/embed.</div>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-brand rounded-pill px-5 fw-bold shadow-sm py-2">
                                <i class="bi bi-check2-circle me-1"></i> Save Branch
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
