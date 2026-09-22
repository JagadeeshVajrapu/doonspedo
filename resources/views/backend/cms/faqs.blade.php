@extends('layouts.admin')

@section('title', 'Manage FAQs')
@section('page_title', 'Homepage Settings › FAQs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.settings.homepage') }}" class="btn btn-light rounded-pill px-3 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Homepage Settings
        </a>
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-patch-question text-brand me-2"></i> Manage FAQs</h5>
    </div>
    <button type="button" class="btn btn-brand rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createFaqModal">
        <i class="bi bi-plus-lg me-1"></i> Add FAQ
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
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Question</th>
                        <th class="py-3 text-muted small fw-bold border-0">Answer Snippet</th>
                        <th class="py-3 text-muted small fw-bold border-0">Visibility</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($faqs) && count($faqs) > 0)
                        @foreach($faqs as $faq)
                        <tr>
                            <td class="fw-bold text-dark w-25"><span class="text-brand me-1">Ques.</span> {{ $faq->question }}</td>
                            <td class="text-muted small w-50"><span class="text-brand fw-bold me-1">Ans.</span> {{ \Illuminate\Support\Str::limit($faq->answer, 80) }}</td>
                            <td>
                                @if($faq->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Visible</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm" title="Edit" data-bs-toggle="modal" data-bs-target="#editFaqModal{{ $faq->id }}">
                                    <i class="bi bi-pencil text-secondary"></i>
                                </button>
                                
                                <form action="{{ route('admin.cms.faqs.destroy', $faq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this FAQ?')">
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
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-question-circle text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No frequently asked questions found. Add one to help your users!</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $faqs->links() }}
        </div>
    </div>
</div>

<!-- Create FAQ Modal -->
<div class="modal fade" id="createFaqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-brand me-2"></i> Add FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.cms.faqs.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Question *</label>
                        <input type="text" class="form-control fw-bold" name="question" placeholder="e.g. How do I request a ride?" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Answer *</label>
                        <textarea class="form-control" name="answer" rows="4" placeholder="Provide a helpful, precise answer..." required></textarea>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="visibleSwitch" checked value="1">
                        <label class="form-check-label fw-bold" for="visibleSwitch">Visible on App/Web</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4 fw-bold">Save FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@if(isset($faqs) && count($faqs) > 0)
@foreach($faqs as $faq)
<!-- Edit FAQ Modal -->
<div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-start">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-brand me-2"></i> Edit FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.cms.faqs.update', $faq->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Question *</label>
                        <input type="text" class="form-control fw-bold" name="question" value="{{ $faq->question }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Answer *</label>
                        <textarea class="form-control" name="answer" rows="4" required>{{ $faq->answer }}</textarea>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="visibleSwitch{{ $faq->id }}" {{ $faq->is_active ? 'checked' : '' }} value="1">
                        <label class="form-check-label fw-bold" for="visibleSwitch{{ $faq->id }}">Visible on App/Web</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4 fw-bold">Update FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection
