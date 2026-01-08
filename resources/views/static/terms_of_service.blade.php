<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: Rubik, sans-serif;
            font-size: 14px;
            margin: 0;
            height: 100vh;
            /* display: flex;
            justify-content: center;
            align-items: center; */
            background-color: #f7fafc;
            color: #4a5568;
        }

        .container {
            border-radius: 8px;
        }

        .home-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .home-button:hover {
            background-color: #0056b3;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h1>Terms of Service</h1>
            </div>
            <div class="card-body">
                <h4>Account Registration</h4>
                <p>To access our content, you must register for an account with a valid email and password. Verification is required via a unique code sent to your email.</p>
                <h4>Account Usage</h4>
                <p>Keep your credentials confidential. Contact us if unauthorized access is suspected.</p>
                <h4>Content Access</h4>
                <p>Verified accounts can log in to access website content. Redistribution is prohibited.</p>
                <p>For further details, contact <a href="mailto:support@thisdatethatyear.com">support@thisdatethatyear.com</a>.</p>
                <div class="text-center mt-4">
                    <a href="{{ route('calendar.loadDefaultCenter') }}" class="btn btn-primary home-button">Back to Home</a>
                </div>
            </div>
        </div>
        <div class="footer-links">
            <a href="{{ route('calendar.loadDefaultCenter') }}">Home</a> | <a href="{{ route('privacy.policy') }}">Privacy Policy</a> | <a href="{{ route('terms.service') }}">Terms of Service</a>
        </div>
    </div>
</body>
</html>
