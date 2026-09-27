@extends('layouts.app')

@section('title', 'My Profile - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
<div class="rider-shell-page">
    <header class="rider-topbar">
        <a href="{{ route('rider.app') }}" class="rider-back-btn" aria-label="Back to booking">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1>My Profile</h1>
    </header>

    <main class="rider-content">
        <div class="rider-card p-4">
            @if(session('success'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 small mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 small mb-4" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('rider.kyc') }}" class="btn btn-outline-dark rounded-pill">KYC status</a>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="text-center mb-4 position-relative">
                    <div class="profile-img-container mx-auto position-relative" style="width: 120px; height: 120px;">
                        @if($user->photo)
                            <img src="{{ asset('uploads/profiles/' . $user->photo) }}" alt="Profile" class="w-100 h-100 rounded-circle object-fit-cover border border-3 border-brand">
                        @else
                            <div class="w-100 h-100 rounded-circle bg-brand d-flex align-items-center justify-content-center border border-3 overflow-hidden">
                                <i class="bi bi-person-fill text-dark display-4"></i>
                            </div>
                        @endif
                        <label for="photo-upload" class="position-absolute bottom-0 end-0 bg-dark text-white rounded-circle p-2 shadow cursor-pointer" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                            <i class="bi bi-camera-fill"></i>
                            <input type="file" id="photo-upload" name="photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </label>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Tap camera to change photo</p>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase ls-1" for="profile-name">Full Name</label>
                    <input type="text" id="profile-name" name="name" class="form-control clean-input" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase ls-1" for="profile-email">Email Address</label>
                    <input type="email" id="profile-email" name="email" class="form-control clean-input" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase ls-1" for="profile-mobile">Mobile Number</label>
                    <input type="tel" id="profile-mobile" name="mobile" class="form-control clean-input" value="{{ old('mobile', $user->mobile) }}" required>
                </div>

                <button type="submit" class="btn btn-brand w-100 py-3 fw-bold rounded-4 shadow active-scale mb-3">
                    Save Changes <i class="bi bi-save2 ms-2" aria-hidden="true"></i>
                </button>
            </form>

            <a href="{{ route('rider.support.index') }}" class="btn btn-outline-brand w-100 py-3 rounded-4 mb-3 fw-bold">
                <i class="bi bi-headset me-2"></i> Help &amp; Support
            </a>

            <div class="mt-4">
                <h2 class="h6 text-muted text-uppercase ls-1 mb-3">App Settings</h2>

                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-4 border">
                    <div class="d-flex align-items-center">
                        <i class="bi {{ session('theme') == 'light' ? 'bi-sun-fill text-warning' : 'bi-moon-stars-fill text-brand' }} fs-4 me-3"></i>
                        <div>
                            <p class="mb-0 small fw-bold">Display Mode</p>
                            <p class="mb-0 x-small text-muted">{{ ucfirst(session('theme', 'dark')) }} Mode Active</p>
                        </div>
                    </div>
                    <a href="{{ route('localization.theme', session('theme') == 'light' ? 'dark' : 'light') }}" class="btn btn-sm btn-dark rounded-pill px-3">Switch</a>
                </div>

                <div class="mb-3">
                    <p class="text-muted x-small fw-bold mb-2 text-uppercase ls-1">Language</p>
                    <div class="d-flex gap-2 flex-wrap">
                        @php $langs = \App\Models\Language::where('is_active', 1)->get(); @endphp
                        @foreach($langs as $lang)
                            <a href="{{ route('localization.locale', $lang->code) }}" class="btn {{ session('locale') == $lang->code ? 'btn-brand' : 'btn-light border' }} rounded-pill px-3 py-2 x-small fw-bold">
                                {{ $lang->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-muted x-small fw-bold mb-2 text-uppercase ls-1">Currency</p>
                    <div class="d-flex gap-2 flex-wrap">
                        @php $currencies = \App\Models\Currency::where('is_active', 1)->get(); @endphp
                        @foreach($currencies as $c)
                            <a href="{{ route('localization.currency', $c->code) }}" class="btn {{ (session('currency')['code'] ?? 'INR') == $c->code ? 'btn-brand' : 'btn-light border' }} rounded-pill px-3 py-2 x-small fw-bold">
                                {{ $c->symbol }} {{ $c->code }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 py-3 rounded-4 fw-bold">
                    Logout <i class="bi bi-box-arrow-right ms-2" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </main>

    @include('partials.ui.rider-bottom-nav', ['active' => 'account'])
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.querySelector('.profile-img-container');
            let img = container.querySelector('img');
            if (!img) {
                const placeholder = container.querySelector('div');
                if (placeholder) placeholder.remove();
                img = document.createElement('img');
                img.className = 'w-100 h-100 rounded-circle object-fit-cover border border-3 border-brand';
                container.prepend(img);
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
.object-fit-cover { object-fit: cover; }
.cursor-pointer { cursor: pointer; }
</style>
@endsection
