<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TDTY - Change Password</title>
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
                        <h4>Change Password</h4>
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

                        <form method="POST" action="{{ route('password.update', ['slug' => $slug]) }}" id="changePasswordForm">
                            @csrf

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password"
                                        name="current_password" required>
                                    <button type="button" class="input-group-text toggle-password"
                                        id="toggle-current_password" data-target="current_password">
                                        <i class="mdi mdi-eye-off"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                        required>
                                    <button type="button" class="input-group-text toggle-password"
                                        id="toggle-new_password" data-target="new_password">
                                        <i class="mdi mdi-eye-off"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password_confirmation"
                                        name="new_password_confirmation" required>
                                    <button type="button" class="input-group-text toggle-password"
                                        id="toggle-new_password_confirmation" data-target="new_password_confirmation">
                                        <i class="mdi mdi-eye-off"></i>
                                    </button>
                                </div>
                            </div>

                            <p style="text-align: center;">
                                <button type="submit" class="btn btn-primary w-30 login-button"
                                    style="margin: auto">Change Password</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const form = document.getElementById('changePasswordForm');
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('new_password_confirmation').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match. Please try again.');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            // toggle confirm_password signup
            const passwordFieldCurrent = document.getElementById('current_password');
            const togglePasswordCurrent = document.getElementById('toggle-current_password');

            togglePasswordCurrent.addEventListener('click', function() {
                const type = passwordFieldCurrent.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordFieldCurrent.setAttribute('type', type);

                // Toggle the eye icon
                this.innerHTML = type === 'password' ? '<i class="mdi mdi-eye-off"></i>' :
                    '<i class="mdi mdi-eye"></i>';
            });

            // toggle password signup
            const passwordFieldNew = document.getElementById('new_password');
            const togglePasswordNew = document.getElementById('toggle-new_password');

            togglePasswordNew.addEventListener('click', function() {
                const type = passwordFieldNew.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordFieldNew.setAttribute('type', type);

                // Toggle the eye icon
                this.innerHTML = type === 'password' ? '<i class="mdi mdi-eye-off"></i>' :
                    '<i class="mdi mdi-eye"></i>';
            });

            // toggle confirm_password signup
            const passwordFieldConfirm = document.getElementById('new_password_confirmation');
            const togglePasswordConfirm = document.getElementById('toggle-new_password_confirmation');

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
