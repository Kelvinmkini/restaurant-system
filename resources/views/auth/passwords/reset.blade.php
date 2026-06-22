@extends('layouts.app')

@section('content')
<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-md-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-warning text-dark text-center py-4 border-0">
                <h3 class="mb-0"><i class="bi bi-key me-2"></i>New Password</h3>
                <p class="mb-0 opacity-75">Enter your new password below</p>
            </div>
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ $email ?? old('email') }}" required readonly>
                        @error('email')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="resetPassword" 
                                   class="form-control @error('password') is-invalid @enderror" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('resetPassword', 'resetEye')">
                                <i class="bi bi-eye" id="resetEye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirmation" id="resetConfirm" 
                                   class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('resetConfirm', 'resetConfirmEye')">
                                <i class="bi bi-eye" id="resetConfirmEye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="bi bi-check-lg me-2"></i>Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(inputId, eyeId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(eyeId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('bi-eye');
        eyeIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('bi-eye-slash');
        eyeIcon.classList.add('bi-eye');
    }
}
</script>
@endpush