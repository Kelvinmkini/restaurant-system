@extends('layouts.app')

@section('content')
<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-md-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-warning text-dark text-center py-4 border-0">
                <h3 class="mb-0"><i class="bi bi-key me-2"></i>Reset Password</h3>
                <p class="mb-0 opacity-75">Enter your email to receive reset link</p>
            </div>
            <div class="card-body p-4 p-md-5">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required autofocus placeholder="your@email.com">
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="bi bi-send me-2"></i>Send Reset Link
                        </button>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection