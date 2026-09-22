@extends('layouts.admin')

@section('title', 'Manage Languages')
@section('page_title', 'Localization Languages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-translate text-brand me-2"></i> System Languages</h5>
    <button type="button" class="btn btn-brand rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createLangModal">
        <i class="bi bi-plus-lg me-1"></i> Add Language
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
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Language Name</th>
                        <th class="py-3 text-muted small fw-bold border-0">Code</th>
                        <th class="py-3 text-muted small fw-bold border-0">Direction</th>
                        <th class="py-3 text-muted small fw-bold border-0">Status</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($languages) && count($languages) > 0)
                        @foreach($languages as $language)
                        <tr>
                            <td class="fw-bold text-dark">{{ $language->name }}</td>
                            <td><span class="badge bg-light text-primary font-monospace px-2 py-1">{{ strtoupper($language->code) }}</span></td>
                            <td>
                                @if($language->direction == 'rtl')
                                    <span class="badge bg-info bg-opacity-10 text-info">RTL</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">LTR</span>
                                @endif
                            </td>
                            <td>
                                @if($language->is_default)
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill">Default</span>
                                @elseif($language->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill">Disabled</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm me-1" title="Edit" data-bs-toggle="modal" data-bs-target="#editLangModal{{ $language->id }}"><i class="bi bi-pencil text-secondary"></i></button>
                                <form action="{{ route('admin.localization.languages.destroy', $language->id) }}" method="POST" onsubmit="return confirm('Delete this language?');" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" title="Delete" {{ $language->is_default ? 'disabled' : '' }}><i class="bi bi-trash text-danger"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-translate text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No languages created yet.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $languages->links() }}
        </div>
    </div>
</div>

<!-- Edit Language Modals -->
@if(isset($languages) && count($languages) > 0)
@foreach($languages as $language)
<div class="modal fade" id="editLangModal{{ $language->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold">Edit Language</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.localization.languages.update', $language->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Language Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $language->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">ISO Code</label>
                        <input type="text" class="form-control" name="code" value="{{ $language->code }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Text Direction</label>
                        <select name="direction" class="form-select">
                            <option value="ltr" {{ $language->direction == 'ltr' ? 'selected' : '' }}>LTR (Left to Right)</option>
                            <option value="rtl" {{ $language->direction == 'rtl' ? 'selected' : '' }}>RTL (Right to Left)</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $language->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold">Active globally</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif

<!-- Create Language Modal -->
<div class="modal fade" id="createLangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-brand me-2"></i> Add Language</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.localization.languages.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Language Name</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. English" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">ISO Code</label>
                        <input type="text" class="form-control" name="code" placeholder="e.g. en" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Text Direction</label>
                        <select name="direction" class="form-select">
                            <option value="ltr">LTR (Left to Right)</option>
                            <option value="rtl">RTL (Right to Left)</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label fw-bold">Active globally</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4">Save Language</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
