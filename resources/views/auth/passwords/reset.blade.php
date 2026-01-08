<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TDTY - Reset Password</title>
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
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center bg-white">
                        <h4>Reset Password</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('reset.password', ['slug' => $slug]) }}" id="resetPasswordForm">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-3">
                                <label for="name" class="form-label">Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="Enter your email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required
                                        placeholder="Set your password">
                                    <button type="button" class="input-group-text toggle-password" id="toggle-password"
                                        data-target="password">
                                        <i class="mdi mdi-eye-off"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="password_confirmation" required placeholder="Confirm your password">
                                    <button type="button" class="input-group-text toggle-password"
                                        id="toggle-password-confirm" data-target="confirm_password">
                                        <i class="mdi mdi-eye-off"></i>
                                    </button>
                                </div>
                            </div>
                            <p style="text-align: center;">
                                <button type="submit" class="btn btn-primary w-30 login-button"
                                    style="margin: auto">Reset Password</button>
                            </p>
                        </form>
                        <p class="mt-3 text-center">
                            <a href="{{ route('calendar.index', ['slug' => $slug]) }}">Back to Dashboard</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.getElementById('resetPasswordForm');
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please try again.');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
       
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


    });
</script>
</body>

</html>
