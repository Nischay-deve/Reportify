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
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center bg-white">
                        <h4>Convert old url to new url!</h4>
                    </div>
                    <div class="card-body">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Old Url <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="old_url" name="old_url"
                                placeholder="Enter your old url" required>
                            <div id="old_url-error" class="error-message"></div>
                        </div>
        
                        <div class="mb-3" id="new_url">
                        </div>
        
                        <p style="text-align: center;">
                            <button type="submit" class="login-button" id="convertButton" disabled>Convert</button>
                        </p>                        

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
        });
    </script>
</body>

</html>
