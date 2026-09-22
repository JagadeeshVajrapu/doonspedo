@extends('layouts.driver')

@section('title', 'Ratings & Feedback - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Ratings & feedback</h1>
        <p class="text-muted mb-0 small">See what your customers are saying about your service.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Star Statistics -->
    <div class="col-lg-4">
        <div class="drv-card p-4 h-100">
            <div class="text-center mb-4">
                <div class="display-3 fw-bold text-dark">{{ number_format($stats['average_rating'], 1) }}</div>
                <div class="text-warning fs-4 mb-1">
                    @php $avg = $stats['average_rating']; @endphp
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= $avg ? '-fill' : ($i - 0.5 <= $avg ? '-half' : '') }}"></i>
                    @endfor
                </div>
                <p class="text-muted small">Based on {{ $stats['total_reviews'] }} reviews</p>
            </div>
            
            <div class="vstack gap-2">
                @foreach([5, 4, 3, 2, 1] as $star)
                    @php 
                        $count = $stats[($star == 5 ? 'five' : ($star == 4 ? 'four' : ($star == 3 ? 'three' : ($star == 2 ? 'two' : 'one')))) . '_star'];
                        $percent = $stats['total_reviews'] > 0 ? ($count / $stats['total_reviews']) * 100 : 0;
                    @endphp
                    <div class="d-flex align-items-center gap-3">
                        <div class="small fw-bold text-muted text-nowrap" style="width: 50px;">{{ $star }} Star</div>
                        <div class="progress flex-grow-1 rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-brand" role="progressbar" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="small text-muted" style="width: 30px;">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Rating Insights -->
    <div class="col-lg-8">
        <div class="row g-4 h-100">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4 h-100">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge text-brand me-2"></i> Performance Tip</h6>
                    <p class="small text-secondary">Maintaining a rating above 4.7 stars makes you eligible for "Top Captain" rewards and higher visibility for premium ride requests.</p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Profile Health</span>
                            <span class="text-brand">Good</span>
                        </div>
                        <div class="progress rounded-pill bg-secondary bg-opacity-25" style="height: 6px;">
                            <div class="progress-bar bg-brand" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 border-start border-4 border-brand">
                    <h6 class="fw-bold mb-2">Driver Etiquette</h6>
                    <p class="small text-muted mb-0">Most passengers appreciate:
                        <ul class="small text-muted ps-3 mt-2 mb-0">
                            <li>Clean vehicle interior</li>
                            <li>Helpful & polite behavior</li>
                            <li>Safe driving habits</li>
                            <li>On-time pickup</li>
                        </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feedback History -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Feedback History</h5>
        <div class="dropdown">
            <button class="btn btn-sm btn-light rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Filter: All Ratings
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                <li><a class="dropdown-item" href="#">5 Stars Only</a></li>
                <li><a class="dropdown-item" href="#">4 Stars Only</a></li>
                <li><a class="dropdown-item text-danger" href="#">Low Ratings</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @if(count($reviews) > 0)
                @foreach($reviews as $review)
                    <div class="list-group-item p-4 border-0 border-bottom">
                        <div class="d-flex">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=f0f0f0&color=999" class="rounded-circle me-3" style="width: 48px; height: 48px;">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="fw-bold mb-0">{{ $review->user->name }}</h6>
                                        <div class="text-warning small">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="small text-muted">{{ $review->created_at->format('d M, Y') }}</span>
                                </div>
                                <div class="bg-light p-3 rounded-4 mt-2 mb-2">
                                    <p class="mb-0 small text-dark fst-italic">"{{ $review->comment ?: 'No written feedback provided.' }}"</p>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="px-2 py-1 bg-white border rounded small text-muted">
                                        <i class="bi bi-hash me-1"></i> Ride #{{ $review->booking_id }}
                                    </div>
                                    <div class="small text-muted">
                                        <i class="bi bi-geo-alt me-1"></i> {{ str($review->booking->dropoff_location)->limit(30) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-star-half display-1 text-muted opacity-25"></i>
                    <h5 class="mt-3 text-muted">No ratings yet</h5>
                    <p class="small text-muted">Complete rides to start receiving feedback from your customers.</p>
                </div>
            @endif
        </div>
    </div>
    @if($reviews->hasPages())
        <div class="card-footer bg-white border-0 p-4">
            {{ $reviews->links() }}
        </div>
    @endif
</div>

<style>
.bg-brand { background-color: #cddc29 !important; }
.text-brand { color: #cddc29 !important; }
.progress-bar.bg-brand { background-color: #cddc29 !important; }
.dropdown-item:active { background-color: #cddc29; color: #000; }
</style>
@endsection
