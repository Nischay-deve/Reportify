<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TDTY</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('js/toastr/toastr.min.css') }}">
    <script src="{{ asset('js/pages/jquery.min.js') }}"></script>
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>


    <style>
        body {
            font-family: Rubik, sans-serif;
            font-size: 14px;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f7fafc;
            color: #4a5568;
        }

        label {
            font-size: 14px;
        }

        .hidden-button {
            position: absolute;
            top: 2px;
            right: 2px;
            background: transparent;
            border: none;
            cursor: pointer;
            width: 50px;
            height: 50px;
        }

        .container {
            text-align: center;
            max-width: 800px;
            width: 85%;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        #login-form {
            margin-top: 20px;
            display: block;
            /* Initially hidden */
        }

        .login-input {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-button:hover {
            background-color: #0056b3;
        }

        .swal2-container {
            z-index: 10000;
        }

        .toast-link {
            color: #ffffff;
            text-decoration: underline;
        }

        .footer-links {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 14px;
        }

        .footer-links a {
            color: #007bff;
            text-decoration: underline;
        }

        .login-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .login-button:disabled:hover {
            background-color: #cccccc;
        }

        .error-input {
            border-color: #dc3545 !important;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .url-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }
        .url-action-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            color: #007bff;
            transition: color 0.3s;
        }
        .url-action-btn:hover {
            color: #0056b3;
        }
        .url-action-btn i {
            font-size: 20px;
        }
        .copied-message {
            color: #28a745;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>

<body>

    @if (session()->has('success'))
        <script>
            // toastr.success('{{ session()->get('success') }}');
            document.addEventListener('DOMContentLoaded', function() {
                toastr.options = {
                    "escapeHtml": false,
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "7000",
                    "extendedTimeOut": "1000",
                };

                toastr.success('{!! session()->get('success') !!}');
            });
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            // toastr.error('{{ session()->get('error') }}');
            document.addEventListener('DOMContentLoaded', function() {
                toastr.options = {
                    "escapeHtml": false,
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "7000",
                    "extendedTimeOut": "1000",
                };

                toastr.error('{!! session()->get('error') !!}');
            });
        </script>
    @endif


    <div class="container">

        @php
            $setting = DB::connection('setfacts')->table('settings')->where('key', 'public_access')->first();

            $publicAccess = $setting ? (bool) $setting->value : false;

            if ($publicAccess || Auth::check() || Cookie::has('remember_token')) {
                header('Location: ' . route('calendar.loadDefaultCenter'));
                exit();
            }
        @endphp

        <!-- Login Form -->
        <div id="login-container">
            <div class="status">
                <div class="status-code" style="font-size: 2em; font-weight: bold;">TDTY</div>
                <div class="status-message" style="font-size: 1.2em;">Login to view reports!</div>
            </div>
            <div id="login-form" style="text-align: left; width:100%;">
                <form method="POST"
                    action="{{ !empty($slug) ? route('authenticate', ['slug' => $slug]) : route('authenticate') }}"
                    id="loginForm">
                    @csrf
                    <input type="hidden" name="mode" value="login">

                    <div class="mb-3">
                        <label for="name" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="email" name="email"
                            placeholder="Enter your email" required>
                        <div id="email-error" class="error-message"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_login" name="password" required
                                placeholder="Enter your password">
                            <button type="button" class="input-group-text" id="toggle-password-login"
                                data-target="password_login">
                                <i class="mdi mdi-eye-off"></i>
                            </button>
                        </div>
                        <div id="login-password-error" class="error-message"></div>
                    </div>

                    <p style="text-align: center;">
                        <button type="submit" class="login-button" id="loginButton" disabled>Login</button>
                    </p>

                </form>

                @if (config('app.allow_mail_sending'))
                    <p style="text-align: center; margin-top: 10px;">
                        <a href="{{ route('forgot.password', ['slug' => $slug]) }}">Forgot Password?</a>
                    </p>
                @endif


                <p style="text-align: center; margin-top: 10px;">
                    <strong>New here?</strong> <a href="javascript:void(0);" id="show-signup-link">Create an account</a>
                </p>

                <p style="text-align: center; margin-top: 10px;">
                    <strong>Have old url?</strong> <a href="javascript:void(0);" id="show-convert-link">Convert old url to new url!</a>
                </p>
            </div>

            <div class="footer-links">
                <a href="{{ route('calendar.loadDefaultCenter') }}">Home</a> | <a
                    href="{{ route('privacy.policy') }}">Privacy Policy</a> | <a
                    href="{{ route('terms.service') }}">Terms of Service</a>
            </div>

        </div>

        <!-- Signup Form -->
        <div id="signup-container" style="display: none;">
            <div class="status">
                <div class="status-code" style="font-size: 2em; font-weight: bold;">TDTY</div>
                <div class="status-message" style="font-size: 1.2em;">Signup to create an account!</div>
            </div>

            <div id="signup-form" style="text-align: left; width:100%;">
                <form id="signupForm" method="POST" action="{{ route('authenticate', ['slug' => $slug]) }}">
                    @csrf
                    <input type="hidden" name="mode" value="signup">

                    <!-- Personal Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Personal Information</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required
                                    placeholder="Enter your name">
                                <div id="name-error" class="error-message"></div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="Enter your email">
                                <div id="signup-email-error" class="error-message"></div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password"
                                        required placeholder="Set your password">
                                    <button type="button" class="input-group-text toggle-password"
                                        id="toggle-password" data-target="password">
                                        <i class="mdi mdi-eye-off"></i>
                                    </button>
                                </div>
                                <div id="signup-password-error" class="error-message"></div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password" required placeholder="Confirm your password">
                                    <button type="button" class="input-group-text toggle-password"
                                        id="toggle-password-confirm" data-target="confirm_password">
                                        <i class="mdi mdi-eye-off"></i>
                                    </button>
                                </div>
                                <div id="confirm-password-error" class="error-message"></div>
                            </div>
                            <div class="mb-3">
                                <label for="mobile_number" class="form-label">Mobile Number <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">+91</span>
                                    <input type="tel" class="form-control" id="mobile_number"
                                        name="mobile_number" required maxlength="10" pattern="[0-9]{10}"
                                        title="Please enter exactly 10 digits mobile number."
                                        placeholder="Enter your mobile number">
                                </div>
                                <div id="mobile-error" class="error-message"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Referred By Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Referred By</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="referred_by_name" class="form-label">Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="referred_by_name"
                                    name="referred_by_name" placeholder="Enter referred by user name">
                            </div>
                            <div class="mb-3">
                                <label for="referred_by_mobile_number" class="form-label">Mobile Number <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon2">+91</span>
                                    <input type="tel" class="form-control" id="referred_by_mobile_number"
                                        name="referred_by_mobile_number" maxlength="10" pattern="[0-9]{10}"
                                        title="Please enter exactly 10 digits mobile number."
                                        placeholder="Enter referred by user mobile number">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <p style="text-align: center;">
                        <button type="submit" class="login-button" id="signupButton" disabled>Sign up</button>
                    </p>
                </form>

                <p style="text-align: center; margin-top: 10px;">
                    <strong>Already have an account?</strong> <a href="javascript:void(0);" id="show-login-link">Back
                        to Login</a>
                </p>
            </div>

            <div class="footer-links">
                <a href="{{ route('calendar.loadDefaultCenter') }}">Home</a> | <a
                    href="{{ route('privacy.policy') }}">Privacy Policy</a> | <a
                    href="{{ route('terms.service') }}">Terms of Service</a>
            </div>
        </div>

        <!-- Convert Form -->
        <div id="convert-container" style="display: none;">
            <div class="status">
                <div class="status-code" style="font-size: 2em; font-weight: bold;">TDTY</div>
                <div class="status-message" style="font-size: 1.2em;">Convert old url to new url!</div>
            </div>
            <div style="text-align: left; width:100%;">
                <div class="mb-3">
                    <label for="name" class="form-label">Old Url <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="old_url" name="old_url"
                        placeholder="Enter your old url" required>
                    <div id="old_url-error" class="error-message"></div>
                </div>

                <div class="mb-3" id="new_url">
                </div>

                <style>
                    .url-actions {
                        display: flex;
                        gap: 10px;
                        justify-content: center;
                        margin-top: 10px;
                    }
                    .url-action-btn {
                        background: none;
                        border: none;
                        cursor: pointer;
                        padding: 5px;
                        color: #007bff;
                        transition: color 0.3s;
                    }
                    .url-action-btn:hover {
                        color: #0056b3;
                    }
                    .url-action-btn i {
                        font-size: 20px;
                    }
                    .copied-message {
                        color: #28a745;
                        font-size: 12px;
                        margin-top: 5px;
                        display: none;
                    }
                </style>

                <p style="text-align: center;">
                    <button type="submit" class="login-button" id="convertButton" disabled>Convert</button>
                </p>


                <p style="text-align: center; margin-top: 10px;">
                    <strong>Already have an account?</strong> <a href="javascript:void(0);" class="show-login-link">Back
                        to Login</a>
                </p>
                <p style="text-align: center; margin-top: 10px;">
                    <strong>New here?</strong> <a href="javascript:void(0);" class="show-signup-link">Create an
                        account</a>
                </p>
            </div>

            <div class="footer-links">
                <a href="{{ route('calendar.loadDefaultCenter') }}">Home</a> | <a
                    href="{{ route('privacy.policy') }}">Privacy Policy</a> | <a
                    href="{{ route('terms.service') }}">Terms of Service</a>
            </div>

        </div>




        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const loginContainer = document.getElementById('login-container');
                const signupContainer = document.getElementById('signup-container');
                const convertContainer = document.getElementById('convert-container');

                const showSignupLink = document.getElementById('show-signup-link');
                const showLoginLink = document.getElementById('show-login-link');
                const showConvertLink = document.getElementById('show-convert-link');

                showSignupLink.addEventListener('click', function() {
                    loginContainer.style.display = 'none';
                    signupContainer.style.display = 'block';
                    convertContainer.style.display = 'none';
                });

                showLoginLink.addEventListener('click', function() {
                    signupContainer.style.display = 'none';
                    loginContainer.style.display = 'block';
                    convertContainer.style.display = 'none';
                });

                showConvertLink.addEventListener('click', function() {
                    loginContainer.style.display = 'none';
                    signupContainer.style.display = 'none';
                    convertContainer.style.display = 'block';
                });

                // Add event listeners for navigation links in convert container
                document.querySelectorAll('.show-login-link').forEach(link => {
                    link.addEventListener('click', function() {
                        convertContainer.style.display = 'none';
                        loginContainer.style.display = 'block';
                        signupContainer.style.display = 'none';
                    });
                });

                document.querySelectorAll('.show-signup-link').forEach(link => {
                    link.addEventListener('click', function() {
                        convertContainer.style.display = 'none';
                        loginContainer.style.display = 'none';
                        signupContainer.style.display = 'block';
                    });
                });

                // toggle password Login
                const passwordFieldLogin = document.getElementById('password_login');
                const togglePasswordLogin = document.getElementById('toggle-password-login');

                togglePasswordLogin.addEventListener('click', function() {
                    const type = passwordFieldLogin.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordFieldLogin.setAttribute('type', type);

                    // Toggle the eye icon
                    this.innerHTML = type === 'password' ? '<i class="mdi mdi-eye-off"></i>' :
                        '<i class="mdi mdi-eye"></i>';
                });

                // toggle password signup
                const passwordField = document.getElementById('password');
                const togglePassword = document.getElementById('toggle-password');

                togglePassword.addEventListener('click', function() {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);

                    // Toggle the eye icon
                    this.innerHTML = type === 'password' ? '<i class="mdi mdi-eye-off"></i>' :
                        '<i class="mdi mdi-eye"></i>';
                });

                // Add URL validation for convert container
                const oldUrlInput = document.getElementById('old_url');
                const convertButton = document.getElementById('convertButton');

                function validateUrl(url) {
                    try {
                        new URL(url);
                        return true;
                    } catch (e) {
                        return false;
                    }
                }

                function checkConvertFormValidity() {
                    const oldUrl = oldUrlInput.value.trim();
                    const isValidUrl = validateUrl(oldUrl);
                    const isCloudfrontUrl = oldUrl.includes('d3ksk6inf0f90y.cloudfront.net/');

                    // Reset error state
                    oldUrlInput.classList.remove('error-input');
                    document.getElementById('old_url-error').classList.remove('show');

                    // Show validation errors if field has been touched
                    if (oldUrlInput.dataset.touched === 'true') {
                        if (!oldUrl) {
                            oldUrlInput.classList.add('error-input');
                            document.getElementById('old_url-error').textContent = 'URL is required';
                            document.getElementById('old_url-error').classList.add('show');
                        } else if (!isValidUrl) {
                            oldUrlInput.classList.add('error-input');
                            document.getElementById('old_url-error').textContent = 'Please enter a valid URL';
                            document.getElementById('old_url-error').classList.add('show');
                        } else if (!isCloudfrontUrl) {
                            oldUrlInput.classList.add('error-input');
                            document.getElementById('old_url-error').textContent = 'URL must be from d3ksk6inf0f90y.cloudfront.net';
                            document.getElementById('old_url-error').classList.add('show');
                        }
                    }

                    // Enable/disable convert button
                    convertButton.disabled = !(oldUrl && isValidUrl && isCloudfrontUrl);

                    if (convertButton.disabled) {
                        convertButton.style.opacity = '0.6';
                        convertButton.style.cursor = 'not-allowed';
                    } else {
                        convertButton.style.opacity = '1';
                        convertButton.style.cursor = 'pointer';
                    }
                }

                // Mark field as touched when user interacts with it
                oldUrlInput.addEventListener('blur', () => {
                    oldUrlInput.dataset.touched = 'true';
                    checkConvertFormValidity();
                });

                oldUrlInput.addEventListener('input', checkConvertFormValidity);

                // Initial check
                checkConvertFormValidity();

                convertButton.addEventListener('click', function() {
                    const oldUrl = document.getElementById('old_url').value;
                    const newUrl = oldUrl.replace('https://d3ksk6inf0f90y.cloudfront.net/', 'https://d3ksk6inf0f90y-medias.s3.ap-south-1.amazonaws.com/');
                    
                    // Create URL display with action buttons
                    const urlContainer = document.createElement('div');
                    urlContainer.innerHTML = `
                        <div style="word-break: break-all; margin-bottom: 10px;">${newUrl}</div>
                        <div class="url-actions">
                            <button class="url-action-btn copy-btn" title="Copy URL">
                                <i class="mdi mdi-content-copy"></i>
                            </button>
                            <button class="url-action-btn" onclick="openUrl('${newUrl}')" title="Open URL">
                                <i class="mdi mdi-open-in-new"></i>
                            </button>
                        </div>
                        <div class="copied-message" id="copiedMessage">URL copied to clipboard!</div>
                    `;
                    
                    document.getElementById('new_url').innerHTML = '';
                    document.getElementById('new_url').appendChild(urlContainer);

                    // Add click event listener for copy button
                    const copyBtn = urlContainer.querySelector('.copy-btn');
                    copyBtn.addEventListener('click', function() {
                        copyUrl(newUrl);
                    });
                });

                // Add copy and open URL functions
                window.copyUrl = function(url) {
                    // Create a temporary input element
                    const tempInput = document.createElement('input');
                    tempInput.value = url;
                    document.body.appendChild(tempInput);
                    
                    // Select and copy the text
                    tempInput.select();
                    tempInput.setSelectionRange(0, 99999); // For mobile devices
                    
                    try {
                        document.execCommand('copy');
                        const copiedMessage = document.getElementById('copiedMessage');
                        copiedMessage.style.display = 'block';
                        setTimeout(() => {
                            copiedMessage.style.display = 'none';
                        }, 2000);
                    } catch (err) {
                        console.error('Failed to copy URL: ', err);
                    }
                    
                    // Clean up
                    document.body.removeChild(tempInput);
                };

                window.openUrl = function(url) {
                    window.open(url, '_blank');
                };

                // toggle confirm_password signup
                const passwordFieldConfirm = document.getElementById('confirm_password');
                const togglePasswordConfirm = document.getElementById('toggle-password-confirm');

                togglePasswordConfirm.addEventListener('click', function() {
                    const type = passwordFieldConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordFieldConfirm.setAttribute('type', type);

                    // Toggle the eye icon
                    this.innerHTML = type === 'password' ? '<i class="mdi mdi-eye-off"></i>' :
                        '<i class="mdi mdi-eye"></i>';
                });

                const loginForm = document.getElementById('loginForm');
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password_login');
                const loginButton = document.getElementById('loginButton');

                // Function to check if form is valid
                function checkFormValidity() {
                    const isEmailValid = emailInput.value.trim() !== '';
                    const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim());
                    const isPasswordValid = passwordInput.value.trim() !== '';

                    // Reset error states
                    emailInput.classList.remove('error-input');
                    passwordInput.classList.remove('error-input');
                    document.getElementById('email-error').classList.remove('show');
                    document.getElementById('login-password-error').classList.remove('show');

                    // Only show email errors if field has been touched
                    if (emailInput.dataset.touched === 'true') {
                        if (!isEmailValid) {
                            emailInput.classList.add('error-input');
                            document.getElementById('email-error').textContent = 'Email is required';
                            document.getElementById('email-error').classList.add('show');
                        } else if (!isValidEmail) {
                            emailInput.classList.add('error-input');
                            document.getElementById('email-error').textContent = 'Please enter a valid email address';
                            document.getElementById('email-error').classList.add('show');
                        } else {
                            document.getElementById('email-error').classList.remove('show');
                        }
                    }

                    // Only show password errors if field has been touched
                    if (passwordInput.dataset.touched === 'true' && !isPasswordValid) {
                        passwordInput.classList.add('error-input');
                        document.getElementById('login-password-error').textContent = 'Password is required';
                        document.getElementById('login-password-error').classList.add('show');
                    } else {
                        document.getElementById('login-password-error').classList.remove('show');
                    }

                    loginButton.disabled = !(isEmailValid && isValidEmail && isPasswordValid);

                    if (loginButton.disabled) {
                        loginButton.style.opacity = '0.6';
                        loginButton.style.cursor = 'not-allowed';
                    } else {
                        loginButton.style.opacity = '1';
                        loginButton.style.cursor = 'pointer';
                    }
                }

                // Mark fields as touched when user interacts with them
                emailInput.addEventListener('blur', () => {
                    emailInput.dataset.touched = 'true';
                    checkFormValidity();
                });

                passwordInput.addEventListener('blur', () => {
                    passwordInput.dataset.touched = 'true';
                    checkFormValidity();
                });

                // Initial check
                checkFormValidity();

                // Signup form validation
                const signupForm = document.getElementById('signupForm');
                const nameInput = document.getElementById('name');
                const signupEmailInput = document.querySelector('#signup-form #email');
                const signupPasswordInput = document.getElementById('password');
                const confirmPasswordInput = document.getElementById('confirm_password');
                const mobileNumberInput = document.getElementById('mobile_number');
                const signupButton = document.getElementById('signupButton');

                // Function to check if signup form is valid
                function checkSignupFormValidity() {
                    const isNameValid = nameInput.value.trim() !== '';
                    const isEmailValid = signupEmailInput.value.trim() !== '';
                    const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(signupEmailInput.value.trim());
                    const isPasswordValid = signupPasswordInput.value.trim() !== '';
                    const isConfirmPasswordValid = confirmPasswordInput.value.trim() !== '';
                    const doPasswordsMatch = signupPasswordInput.value === confirmPasswordInput.value;
                    const isMobileValid = mobileNumberInput.value.trim() !== '' && /^[0-9]{10}$/.test(mobileNumberInput.value.trim());

                    // Reset error states
                    nameInput.classList.remove('error-input');
                    signupEmailInput.classList.remove('error-input');
                    signupPasswordInput.classList.remove('error-input');
                    confirmPasswordInput.classList.remove('error-input');
                    mobileNumberInput.classList.remove('error-input');
                    
                    document.getElementById('name-error').classList.remove('show');
                    document.getElementById('signup-email-error').classList.remove('show');
                    document.getElementById('signup-password-error').classList.remove('show');
                    document.getElementById('confirm-password-error').classList.remove('show');
                    document.getElementById('mobile-error').classList.remove('show');

                    // Validate name
                    if (nameInput.dataset.touched === 'true' && !isNameValid) {
                        nameInput.classList.add('error-input');
                        document.getElementById('name-error').textContent = 'Name is required';
                        document.getElementById('name-error').classList.add('show');
                    }

                    // Validate email
                    if (signupEmailInput.dataset.touched === 'true') {
                        if (!isEmailValid) {
                            signupEmailInput.classList.add('error-input');
                            document.getElementById('signup-email-error').textContent = 'Email is required';
                            document.getElementById('signup-email-error').classList.add('show');
                        } else if (!isValidEmail) {
                            signupEmailInput.classList.add('error-input');
                            document.getElementById('signup-email-error').textContent = 'Please enter a valid email address';
                            document.getElementById('signup-email-error').classList.add('show');
                        }
                    }

                    // Validate password
                    if (signupPasswordInput.dataset.touched === 'true' && !isPasswordValid) {
                        signupPasswordInput.classList.add('error-input');
                        document.getElementById('signup-password-error').textContent = 'Password is required';
                        document.getElementById('signup-password-error').classList.add('show');
                    }

                    // Validate confirm password
                    if (confirmPasswordInput.dataset.touched === 'true') {
                        if (!isConfirmPasswordValid) {
                            confirmPasswordInput.classList.add('error-input');
                            document.getElementById('confirm-password-error').textContent = 'Confirm password is required';
                            document.getElementById('confirm-password-error').classList.add('show');
                        } else if (!doPasswordsMatch) {
                            confirmPasswordInput.classList.add('error-input');
                            document.getElementById('confirm-password-error').textContent = 'Passwords do not match';
                            document.getElementById('confirm-password-error').classList.add('show');
                        }
                    }

                    // Validate mobile number
                    if (mobileNumberInput.dataset.touched === 'true') {
                        if (!mobileNumberInput.value.trim()) {
                            mobileNumberInput.classList.add('error-input');
                            document.getElementById('mobile-error').textContent = 'Mobile number is required';
                            document.getElementById('mobile-error').classList.add('show');
                        } else if (!/^[0-9]{10}$/.test(mobileNumberInput.value.trim())) {
                            mobileNumberInput.classList.add('error-input');
                            document.getElementById('mobile-error').textContent = 'Please enter a valid 10-digit mobile number';
                            document.getElementById('mobile-error').classList.add('show');
                        }
                    }

                    // Enable/disable signup button
                    signupButton.disabled = !(isNameValid && isEmailValid && isValidEmail && 
                                          isPasswordValid && isConfirmPasswordValid && doPasswordsMatch && isMobileValid);

                    if (signupButton.disabled) {
                        signupButton.style.opacity = '0.6';
                        signupButton.style.cursor = 'not-allowed';
                    } else {
                        signupButton.style.opacity = '1';
                        signupButton.style.cursor = 'pointer';
                    }
                }

                // Mark fields as touched when user interacts with them
                nameInput.addEventListener('blur', () => {
                    nameInput.dataset.touched = 'true';
                    checkSignupFormValidity();
                });

                signupEmailInput.addEventListener('blur', () => {
                    signupEmailInput.dataset.touched = 'true';
                    checkSignupFormValidity();
                });

                signupPasswordInput.addEventListener('blur', () => {
                    signupPasswordInput.dataset.touched = 'true';
                    checkSignupFormValidity();
                });

                confirmPasswordInput.addEventListener('blur', () => {
                    confirmPasswordInput.dataset.touched = 'true';
                    checkSignupFormValidity();
                });

                mobileNumberInput.addEventListener('blur', () => {
                    mobileNumberInput.dataset.touched = 'true';
                    checkSignupFormValidity();
                });

                // Add input listeners for real-time validation
                nameInput.addEventListener('input', checkSignupFormValidity);
                signupEmailInput.addEventListener('input', checkSignupFormValidity);
                signupPasswordInput.addEventListener('input', checkSignupFormValidity);
                confirmPasswordInput.addEventListener('input', checkSignupFormValidity);
                mobileNumberInput.addEventListener('input', checkSignupFormValidity);

                // Initial check for signup form
                checkSignupFormValidity();
            });
        </script>

    </div>

</body>

</html>
