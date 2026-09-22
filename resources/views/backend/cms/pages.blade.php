@extends('layouts.admin')

@section('title', 'Manage Static Pages')
@section('page_title', 'Static Pages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-richtext text-brand me-2"></i> Content Pages</h5>
    <button type="button" class="btn btn-brand rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createPageModal">
        <i class="bi bi-plus-lg me-1"></i> Create Page
    </button>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Page Title</th>
                        <th class="py-3 text-muted small fw-bold border-0">URL Slug</th>
                        <th class="py-3 text-muted small fw-bold border-0">Status</th>
                        <th class="py-3 text-muted small fw-bold border-0">Last Updated</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($pages) && count($pages) > 0)
                        @foreach($pages as $page)
                        <tr>
                            <td class="fw-bold">{{ $page->title }}</td>
                            <td><span class="badge bg-light text-primary user-select-all px-2 py-1"><i class="bi bi-link-45deg me-1"></i>/{{ $page->slug }}</span></td>
                            <td>
                                @if($page->is_published)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Published</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1">Draft</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $page->updated_at->format('M d, Y h:i A') }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm" title="Edit" data-bs-toggle="modal" data-bs-target="#editPageModal{{ $page->id }}">
                                    <i class="bi bi-pencil text-secondary"></i>
                                </button>
                                
                                <form action="{{ route('admin.cms.pages.destroy', $page->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this page?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm ms-1" title="Delete">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-file-earmark-x text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No static pages found. Create a Privacy Policy or Terms of Service page.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $pages->links() }}
        </div>
    </div>
</div>

<!-- Create Page Modal -->
<div class="modal fade" id="createPageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-brand me-2"></i> New Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.cms.pages.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Page Title *</label>
                            <input type="text" class="form-control" name="title" placeholder="e.g. Privacy Policy" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">URL Slug *</label>
                            <input type="text" class="form-control" name="slug" placeholder="e.g. privacy-policy" required>
                            <div class="form-text small">Use lowercase letters and hyphens only</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Page Content *</label>
                        <textarea class="form-control" name="content" rows="8" placeholder="Enter HTML or plain text content..." required></textarea>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_published" id="publishSwitch" checked value="1">
                        <label class="form-check-label fw-bold" for="publishSwitch">Publish Immediately</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4 fw-bold">Save Page</button>
                </div>
            </form>
        </div>
    </div>
</div>
@if(isset($pages) && count($pages) > 0)
@foreach($pages as $page)
<!-- Edit Page Modal -->
<div class="modal fade" id="editPageModal{{ $page->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-start">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-brand me-2"></i> Edit Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.cms.pages.update', $page->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Page Title *</label>
                            <input type="text" class="form-control" name="title" value="{{ $page->title }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">URL Slug *</label>
                            <input type="text" class="form-control" name="slug" value="{{ $page->slug }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Page Content *</label>
                        <textarea class="form-control" name="content" rows="8" required>{{ $page->content }}</textarea>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_published" id="publishSwitch{{ $page->id }}" {{ $page->is_published ? 'checked' : '' }} value="1">
                        <label class="form-check-label fw-bold" for="publishSwitch{{ $page->id }}">Published</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4 fw-bold">Update Page</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection
