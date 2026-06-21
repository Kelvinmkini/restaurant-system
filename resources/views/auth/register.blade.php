@extends('layouts.app')

@section('content')
<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-md-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white text-center py-4 border-0">
                <h3 class="mb-0"><i class="bi bi-shop-window me-2"></i>Restaurant Manager</h3>
                <p class="mb-0 opacity-75">Create new account</p>
            </div>
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required autofocus placeholder="Your full name">
                        </div>
                        @error('name')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required placeholder="your@email.com">
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   required placeholder="Min 8 characters">
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Sign In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
