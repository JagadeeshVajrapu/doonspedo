@extends('layouts.admin')

@section('title', 'Homepage Settings')
@section('page_title', 'Manage Homepage Sections')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-house-gear text-brand me-2"></i> Homepage Settings</h5>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4 px-4 py-3">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.settings.homepage.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- Vertical Tabs menu -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="nav flex-column nav-pills custom-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link text-start active fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-hero-tab" data-bs-toggle="pill" data-bs-target="#v-pills-hero" type="button" role="tab">
                            <i class="bi bi-window-stack me-2"></i> Hero Slider
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-about-tab" data-bs-toggle="pill" data-bs-target="#v-pills-about" type="button" role="tab">
                            <i class="bi bi-info-circle me-2"></i> About Us
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-wcu-tab" data-bs-toggle="pill" data-bs-target="#v-pills-wcu" type="button" role="tab">
                            <i class="bi bi-stars me-2"></i> Why Choose Us
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 mb-2 rounded-3" id="v-pills-faq-tab" data-bs-toggle="pill" data-bs-target="#v-pills-faq" type="button" role="tab">
                            <i class="bi bi-patch-question me-2"></i> FAQ Section
                        </button>
                        <button class="nav-link text-start fw-bold py-3 px-4 rounded-3" id="v-pills-popup-tab" data-bs-toggle="pill" data-bs-target="#v-pills-popup" type="button" role="tab">
                            <i class="bi bi-megaphone me-2"></i> Popup Ad
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Content -->
        <div class="col-md-9">
            <div class="admin-card">
                <div class="card-body p-5">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- Hero Slider Panel -->
                        <div class="tab-pane fade show active" id="v-pills-hero" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-window-stack text-muted me-2"></i> Hero / Slider Section</h5>
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Hero Main Title (HTML allowed)</label>
                                    <input type="text" name="hero_title" class="form-control" value="{{ $settings['hero_title'] ?? 'BOOK YOUR <br><span class=\"text-brand\">RIDE</span>' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Hero Subtitle / Lead Paragraph</label>
                                    <textarea name="hero_subtitle" class="form-control" rows="3">{{ $settings['hero_subtitle'] ?? 'Experience the most premium taxi service in the city with transparent pricing and professional drivers.' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">CTA Button Text</label>
                                    <input type="text" name="hero_cta_text" class="form-control" value="{{ $settings['hero_cta_text'] ?? 'BOOK YOUR RIDE' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">CTA Link</label>
                                    <input type="text" name="hero_cta_link" class="form-control" value="{{ $settings['hero_cta_link'] ?? '' }}">
                                </div>
                                <div class="col-12 mt-4">
                                    <label class="form-label small fw-bold text-muted">Hero Image</label>
                                    <div class="bg-light p-4 rounded-4 border border-dashed text-center">
                                        @if(isset($settings['hero_image']))
                                            <img src="{{ asset($settings['hero_image']) }}" alt="Hero" class="rounded shadow-sm mb-3" style="max-height: 150px;">
                                        @endif
                                        <input type="file" name="hero_image" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- About Us Panel -->
                        <div class="tab-pane fade" id="v-pills-about" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-info-circle text-muted me-2"></i> About Us Section</h5>
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Section Title</label>
                                    <input type="text" name="about_title" class="form-control" value="{{ $settings['about_title'] ?? 'Premium Taxi Services' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Description</label>
                                    <textarea name="about_description" class="form-control" rows="5">{{ $settings['about_description'] ?? 'We provide reliable and comfortable transportation solutions...' }}</textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <label class="form-label small fw-bold text-muted">About Section Image</label>
                                    <div class="bg-light p-4 rounded-4 border border-dashed text-center">
                                        @if(isset($settings['about_image']))
                                            <img src="{{ asset($settings['about_image']) }}" alt="About" class="rounded shadow-sm mb-3" style="max-height: 150px;">
                                        @endif
                                        <input type="file" name="about_image" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Why Choose Us Panel -->
                        <div class="tab-pane fade" id="v-pills-wcu" role="tabpanel">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-stars text-muted me-2"></i> Why Choose Us Section</h5>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Section Heading</label>
                                    <input type="text" name="wcu_title" class="form-control" value="{{ $settings['wcu_title'] ?? 'WHY <span class=\"text-brand\">CHOOSE US?</span>' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Subheading</label>
                                    <input type="text" name="wcu_subtitle" class="form-control" value="{{ $settings['wcu_subtitle'] ?? '' }}">
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Feature 1 -->
                                <div class="col-12 border-bottom pb-4 mb-4">
                                    <h6 class="fw-bold mb-3">Feature 1</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small">Icon (Bootstrap Icon Class)</label>
                                            <input type="text" name="wcu_feature1_icon" class="form-control" value="{{ $settings['wcu_feature1_icon'] ?? 'bi-clock-history' }}">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label small">Title</label>
                                            <input type="text" name="wcu_feature1_title" class="form-control" value="{{ $settings['wcu_feature1_title'] ?? '24/7 Service' }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Description</label>
                                            <input type="text" name="wcu_feature1_desc" class="form-control" value="{{ $settings['wcu_feature1_desc'] ?? 'Always available when you need us, day or night across the city.' }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature 2 -->
                                <div class="col-12 border-bottom pb-4 mb-4">
                                    <h6 class="fw-bold mb-3">Feature 2</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small">Icon</label>
                                            <input type="text" name="wcu_feature2_icon" class="form-control" value="{{ $settings['wcu_feature2_icon'] ?? 'bi-shield-check' }}">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label small">Title</label>
                                            <input type="text" name="wcu_feature2_title" class="form-control" value="{{ $settings['wcu_feature2_title'] ?? 'Safe Rides' }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Description</label>
                                            <input type="text" name="wcu_feature2_desc" class="form-control" value="{{ $settings['wcu_feature2_desc'] ?? 'Verified drivers and real-time tracking for your complete peace of mind.' }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature 3 -->
                                <div class="col-12">
                                    <h6 class="fw-bold mb-3">Feature 3</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small">Icon</label>
                                            <input type="text" name="wcu_feature3_icon" class="form-control" value="{{ $settings['wcu_feature3_icon'] ?? 'bi-currency-rupee' }}">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label small">Title</label>
                                            <input type="text" name="wcu_feature3_title" class="form-control" value="{{ $settings['wcu_feature3_title'] ?? 'Best Price' }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Description</label>
                                            <input type="text" name="wcu_feature3_desc" class="form-control" value="{{ $settings['wcu_feature3_desc'] ?? 'Transparent pricing with no hidden charges. Pay what you see.' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Section Panel -->
                        <div class="tab-pane fade" id="v-pills-faq" role="tabpanel">
                            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-patch-question text-muted me-2"></i> FAQ Section</h5>
                            <p class="text-muted small mb-4">Homepage par dikhne wale FAQs yahan se manage karein.</p>

                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-brand rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createFaqModalHP">
                                    <i class="bi bi-plus-lg me-1"></i> Add FAQ
                                </button>
                            </div>

                            @if(session('faq_success'))
                                <div class="alert alert-success rounded-4 border-0 mb-3">{{ session('faq_success') }}</div>
                            @endif

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
                                                    <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" title="Edit" aria-label="Edit FAQ" data-bs-toggle="modal" data-bs-target="#editFaqModalHP{{ $faq->id }}">
                                                        <i class="bi bi-pencil text-secondary" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm ms-1" title="Delete" aria-label="Delete FAQ" onclick="deleteFaq({{ $faq->id }})">
                                                        <i class="bi bi-trash text-danger" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <i class="bi bi-question-circle text-muted" style="font-size:2rem;"></i>
                                                    <p class="text-muted mt-2 mb-0">No FAQs found. Add one!</p>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $faqs->links() }}</div>
                        </div>

                        <!-- Popup Ad Panel -->
                        <div class="tab-pane fade" id="v-pills-popup" role="tabpanel">
                            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-megaphone text-muted me-2"></i> Popup Ad Settings</h5>
                            <p class="text-muted small mb-4">Homepage par dikhne wala promotion popup yahan se manage karein.</p>

                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="form-check form-switch bg-light p-3 rounded-4 border">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="popup_enabled" id="popup_enabled" value="1" {{ ($settings['popup_enabled'] ?? '') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="popup_enabled">Enable Popup Ad</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Popup Title (Optional)</label>
                                    <input type="text" name="popup_title" class="form-control" value="{{ $settings['popup_title'] ?? '' }}" placeholder="e.g. Special Offer!">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Popup Link / Redirect URL</label>
                                    <input type="text" name="popup_link" class="form-control" value="{{ $settings['popup_link'] ?? '' }}" placeholder="https://example.com/promo">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Display Delay (Seconds)</label>
                                    <input type="number" name="popup_delay" class="form-control" value="{{ $settings['popup_delay'] ?? '2' }}" min="0" max="60">
                                    <div class="form-text">Website load hone ke kitne der baad popup dikhana hai.</div>
                                </div>
                                <div class="col-12 mt-4">
                                    <label class="form-label small fw-bold text-muted">Popup Image</label>
                                    <div class="bg-light p-4 rounded-4 border border-dashed text-center">
                                        @if(isset($settings['popup_image']))
                                            <div class="mb-3 position-relative d-inline-block">
                                                <img src="{{ asset($settings['popup_image']) }}" alt="Popup Ad" class="rounded shadow-sm" style="max-height: 200px; max-width: 100%;">
                                            </div>
                                        @endif
                                        <input type="file" name="popup_image" class="form-control">
                                        <div class="form-text mt-2 text-danger small">Preferred size: 600x400px or similar ratio.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer bg-white border-top border-light p-4 text-end rounded-bottom-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm py-2">
                        <i class="bi bi-check2-circle me-1"></i> Update Homepage
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Create FAQ Modal --}}
<div class="modal fade" id="createFaqModalHP" tabindex="-1" aria-hidden="true">
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
                        <input type="text" class="form-control fw-bold" name="question" placeholder="e.g. How do I book a ride?" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Answer *</label>
                        <textarea class="form-control" name="answer" rows="4" placeholder="Provide a helpful answer..." required></textarea>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="visibleSwitchHP" checked value="1">
                        <label class="form-check-label fw-bold" for="visibleSwitchHP">Visible on Homepage</label>
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

{{-- Edit FAQ Modals --}}
@if(isset($faqs) && count($faqs) > 0)
@foreach($faqs as $faq)
<div class="modal fade" id="editFaqModalHP{{ $faq->id }}" tabindex="-1" aria-hidden="true">
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
                        <input class="form-check-input" type="checkbox" name="is_active" id="visibleSwitchHP{{ $faq->id }}" {{ $faq->is_active ? 'checked' : '' }} value="1">
                        <label class="form-check-label fw-bold" for="visibleSwitchHP{{ $faq->id }}">Visible on Homepage</label>
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

<style>
.custom-pills .nav-link {
    color: #495057;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}
.custom-pills .nav-link:hover {
    background-color: rgba(0,0,0,0.03);
}
.custom-pills .nav-link.active {
    background-color: #f8f9fa;
    color: var(--admin-primary, #007bff);
    border-left: 3px solid var(--admin-primary, #007bff);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab === 'faq') {
        const faqTab = document.querySelector('#v-pills-faq-tab');
        if (faqTab) {
            // Deactivate current active tab
            document.querySelectorAll('#settings-tab .nav-link.active').forEach(el => {
                el.classList.remove('active');
                const target = el.getAttribute('data-bs-target');
                if (target) {
                    const pane = document.querySelector(target);
                    if (pane) pane.classList.remove('show', 'active');
                }
            });
            // Activate FAQ tab
            faqTab.classList.add('active');
            const faqPane = document.querySelector('#v-pills-faq');
            if (faqPane) faqPane.classList.add('show', 'active');
        }
    }
});

function deleteFaq(id) {
    if (confirm('Delete this FAQ?')) {
        const form = document.getElementById('delete-faq-form');
        form.action = `/admin/cms/faqs/${id}`;
        form.submit();
    }
}
</script>

<form id="delete-faq-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection
