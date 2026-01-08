<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TDTY - Forgot Password</title>
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
                    <h4>Forgot Password</h4>
                    <p>Enter your email address, and we'll send you a link to reset your password.</p>
                </div>
                <div class="card-body">
                    @if (session()->has('success'))
                        <script>
                            toastr.success('{{ session()->get('success') }}');
                        </script>
                    @endif

                    @if (session()->has('error'))
                        <script>
                            toastr.error('{{ session()->get('error') }}');
                        </script>
                    @endif

                    <form method="POST" action="{{ route('forgot.password.email', ['slug' => $slug]) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                        </div>

                        <p style="text-align: center;">                                                    
                            <button type="submit" class="btn btn-primary w-30 login-button" style="margin: auto">Send Reset Password Link</button>
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

