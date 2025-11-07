<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Up | Finance App</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('auth/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('auth/css/fontawesome-simple.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('auth/css/auth.min.css') }}" rel="stylesheet">
    <link href="{{ asset('auth/css/auth-fixes.css') }}" rel="stylesheet">
</head>
<body>
    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Create New Account</h5>
                                    <p class="text-muted">Sign up to get started with Finance App</p>
                                </div>

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="p-2 mt-4">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name') }}"
                                                   placeholder="Enter your full name" required autocomplete="name" autofocus>

                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email') }}"
                                                   placeholder="Enter email address" required autocomplete="email">

                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                                       id="password" name="password" placeholder="Enter password"
                                                       required autocomplete="new-password">
                                                <button class="btn btn-link position-absolute text-decoration-none text-muted password-addon"
                                                        type="button" id="password-addon">
                                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                    </svg>
                                                </button>

                                                @error('password')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password-confirm">Confirm Password <span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 password-input @error('password_confirmation') is-invalid @enderror"
                                                       id="password-confirm" name="password_confirmation" placeholder="Confirm password"
                                                       required autocomplete="new-password">
                                                <button class="btn btn-link position-absolute text-decoration-none text-muted password-addon-confirm"
                                                        type="button" id="password-addon-confirm">
                                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                    </svg>
                                                </button>

                                                @error('password_confirmation')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div id="password-contain" class="p-3 bg-light mb-3 rounded" style="display: none;">
                                            <h6 class="fs-14 mb-3">Password must contain:</h6>
                                            <div class="d-flex align-items-center mb-2">
                                                <svg width="16" height="16" class="me-2" id="pass-length-icon" fill="currentColor" viewBox="0 0 16 16">
                                                    <circle cx="8" cy="8" r="8"/>
                                                </svg>
                                                <span id="pass-length" class="fs-13">Minimum <b>8 characters</b></span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <svg width="16" height="16" class="me-2" id="pass-lower-icon" fill="currentColor" viewBox="0 0 16 16">
                                                    <circle cx="8" cy="8" r="8"/>
                                                </svg>
                                                <span id="pass-lower" class="fs-13">At <b>lowercase</b> letter (a-z)</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <svg width="16" height="16" class="me-2" id="pass-upper-icon" fill="currentColor" viewBox="0 0 16 16">
                                                    <circle cx="8" cy="8" r="8"/>
                                                </svg>
                                                <span id="pass-upper" class="fs-13">At least <b>uppercase</b> letter (A-Z)</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-0">
                                                <svg width="16" height="16" class="me-2" id="pass-number-icon" fill="currentColor" viewBox="0 0 16 16">
                                                    <circle cx="8" cy="8" r="8"/>
                                                </svg>
                                                <span id="pass-number" class="fs-13">A least <b>number</b> (0-9)</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                                                <label class="form-check-label" for="terms">
                                                    I agree to the <a href="#" class="text-primary">Terms & Conditions</a>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-info w-100">
                                                Sign Up
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="mb-0 text-white-50">
                                Already have an account?
                                <a href="{{ route('login') }}" class="fw-semibold text-white text-decoration-underline">Signin</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center text-white-50">
                            <p class="mb-0">© {{ date('Y') }} Finance App. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('auth/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('auth/js/auth.min.js') }}"></script>

    <script>
        // Initialize password toggles for register page
        document.addEventListener('DOMContentLoaded', function() {
            // Use the global function from auth.min.js for main password
            if (typeof initPasswordToggle === 'function') {
                initPasswordToggle('password-addon', 'password');
                initPasswordToggle('password-addon-confirm', 'password-confirm');
            }

            // Password strength indicator
            const passwordInput = document.getElementById('password');
            const passwordContainer = document.getElementById('password-contain');

            if (passwordInput && passwordContainer) {
                // Show/hide password strength indicator
                passwordInput.addEventListener('focus', function() {
                    passwordContainer.style.display = 'block';
                });

                passwordInput.addEventListener('blur', function() {
                    if (this.value.length === 0) {
                        passwordContainer.style.display = 'none';
                    }
                });

                // Password strength validation
                function validatePassword(password) {
                    const validations = {
                        length: password.length >= 8,
                        lowercase: /[a-z]/.test(password),
                        uppercase: /[A-Z]/.test(password),
                        number: /[0-9]/.test(password)
                    };

                    // Update validation indicators with checkmarks
                    updateValidationIcon('pass-length-icon', validations.length);
                    updateValidationIcon('pass-lower-icon', validations.lowercase);
                    updateValidationIcon('pass-upper-icon', validations.uppercase);
                    updateValidationIcon('pass-number-icon', validations.number);

                    return Object.values(validations).every(Boolean);
                }

                function updateValidationIcon(iconId, isValid) {
                    const icon = document.getElementById(iconId);
                    if (icon) {
                        if (isValid) {
                            icon.innerHTML = '<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>';
                            icon.style.color = '#28a745';
                        } else {
                            icon.innerHTML = '<circle cx="8" cy="8" r="8"/>';
                            icon.style.color = '#dc3545';
                        }
                    }
                }

                // Real-time password validation
                passwordInput.addEventListener('input', function() {
                    validatePassword(this.value);
                });
            }
        });
    </script>
</body>
</html>