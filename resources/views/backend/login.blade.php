@php $noSidebar = true; @endphp
@extends('layouts.admin')

@section('title', 'Admin Login')

@section('content')
<div class="login-container container" id="main-content">
    <div class="row justify-content-center w-100">
        <div class="col-md-5 col-lg-4">
            <div class="login-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        @include('partials.ui.brand-logo', ['size' => 'md'])
                    </div>
                    <h1 class="h3 text-dark fw-bold mb-1">Admin access</h1>
                    <p class="text-muted small mb-0">Manage your Doonspedo operations panel</p>
                </div>

                @if($errors->any())
                    @include('partials.ui.alert', ['variant' => 'danger', 'message' => $errors->first()])
                @endif

                <form action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark small fw-bold" for="email">Email address</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@doonspedo.com" required autocomplete="username" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                        @error('email')
                            <div class="invalid-feedback" id="email-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark small fw-bold" for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                        @error('password')
                            <div class="invalid-feedback" id="password-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-brand btn-lg rounded-pill fw-bold py-3">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
