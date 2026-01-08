<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1"> --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{config('app.name')}}</title>

    <!-- Scripts -->
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('js/pages/jquery-1.11.3.min.js') }}"></script>
    <script src="{{ asset('js/pages/jquery.min.js') }}"></script>
    <script src="{{ asset('js/pages/select2.min.js') }}"></script>
    <script src="{{ asset('js/validation/jquery.validate.min.js') }}"></script>
    <link href="{{ asset('css/select2.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">

    <script src="{{ asset('libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

    @if (config('app.color_scheme') == 'pink')
        <style>
            .btn-pink:hover {
                color: #fff;
                background-color: #FF6B1F !important;
                border-color: #FF6B1F !important;
            }

            .btn-check:focus+.btn-pink,
            .btn-pink:focus {
                color: #fff;
                background-color: #FF6B1F !important;
                border-color: #FF6B1F !important;
                -webkit-box-shadow: 0 0 0 0.15rem rgb(255 126 170 / 50%);
                box-shadow: 0 0 0 0.15rem rgb(255 126 170 / 50%);
            }

            .btn-check:active+.btn-pink,
            .btn-check:checked+.btn-pink,
            .btn-pink.active,
            .btn-pink:active,
            .show>.btn-pink.dropdown-toggle {
                color: #fff;
                background-color: #FF6B1F !important;
                border-color: #FF6B1F !important;
            }


            .nav-pills .nav-link.active,
            .nav-pills .show>.nav-link {
                color: #fff;
                background-color: #FF6B1F !important;
                font-weight: bold;
            }


        </style>
    @endif

    <script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property={{config('app.share_this_property')}}&product=sop' async='async'></script>
</head>

<body class="auth-body-bg">
    <div id="app" class="page-wrapper bg-{{ config('app.color_scheme') }} p-t-130 p-b-100">


        <style>
            .invalid-feedback {
                display: block !important;
            }

            .select2-container--default .select2-selection--single {
                height: 35px !important;
                border: 1px solid #ced4da;
            }

            .select2-container .select2-selection--multiple {
                min-height: 35px !important;
            }
        </style>

        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col-md-12" style="text-align: center">
                    <div class="loginLogoContainer mb-3">
                    <a href="{{ route('calendar.index', $website_slug) }}" class="logo logo-admin"><img
                            src="{{ asset('images/logo.jpg') }}" height="60" alt="logo"></a>
                    </div>
                </div>
            </div>

            <div class="row flex-column-reverse flex-md-row"  style="display : flex;flex-direction : row;">
                <div class="col-md-4"></div>
                <div class="col-md-4">

                    <div class="card card-4 cardFullHeight">
                        <div class="card-body">

                            @if (session()->has('success'))
                                <div class="alert alert-success">
                                    {{ session()->get('success') }}
                                </div>
                            @elseif(session()->has('error'))
                                <div class="alert alert-danger">
                                    {{ session()->get('error') }}
                                </div>
                            @endif

                            <!-- Pills navs -->
                            <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">                               
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-login" data-bs-toggle="pill" href="#pills-login"
                                        role="tab" aria-controls="pills-login" aria-selected="true">Login</a>
                                </li>                               
                            </ul>
                            <!-- Pills navs -->

                            <!-- Pills content -->
                            <div class="tab-content">

                                {{-- Login Form --}}
                                <div class="tab-pane fade show active" id="pills-login" role="tabpanel"
                                    aria-labelledby="tab-login">

                                    <h3>Login to create, edit & manage reports!</h3>

                                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                                        @csrf

                                        <div class="form-group">
                                            <label for="email"
                                                class="col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}" required
                                                autocomplete="email" autofocus>

                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>

                                        <div class="form-group">
                                            <label for="password"
                                                class="col-form-label text-md-right">{{ __('Password') }}</label>


                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password" required autocomplete="current-password">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>


                                        <div class="form-group row mb-0" style="margin-top: 20px;">
                                            <div class="col-md-12 ">
                                                <button type="submit"
                                                    class="btn btn-{{ config('app.color_scheme') }}">
                                                    {{ __('Login') }}
                                                </button>
                                            </div>
                                        </div>

                                    </form>

                                </div>
                              
                            </div>
                            <!-- Pills content -->
                        </div>
                    </div>

                </div>
               
                <div class="col-md-4"></div>
            </div>


        </div>




    </div>

    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>


    <script>
        function matchCustom(params, data) {
            // If there are no search terms, return all of the data
            if ($.trim(params.term) === '') {
                return data;
            }

            // Do not display the item if there is no 'text' property
            if (typeof data.text === 'undefined') {
                return null;
            }

            // `params.term` should be the term that is used for searching
            // `data.text` is the text that is displayed for the data object
            if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) == 0) {
                return data;
            }

            // custom search using lookup data
            if (typeof $(data.element).data('lookup') !== 'undefined' && $(data.element).data('lookup').toUpperCase()
                .indexOf(params.term.toUpperCase()) == 0) {
                return data;
            }

            // Return `null` if the term should not be displayed
            return null;
        }

        function showRegister(){
            activeTab('pills-register');
            $('#tab-register').trigger('click');            
        }

        function showLogin(){
            activeTab('pills-login');
            $('#tab-login').trigger('click');            
        }


        function activeTab(tab) {
            $('.nav-pills a[href="#' + tab + '"]').tab('show');
        };

        $(document).ready(function() {

            // $('#tab-login').on('click', function() {
            //     $('#1stCol').removeClass('col-md-1');
            //     $('#1stCol').addClass('col-md-4');

            //     $('#2ndCol').removeClass('col-md-10');
            //     $('#2ndCol').addClass('col-md-4');

            //     $('#3rdCol').removeClass('col-md-1');
            //     $('#3rdCol').addClass('col-md-4');
            // });

            // $('#tab-register').on('click', function() {
            //     $('#1stCol').removeClass('col-md-1');
            //     $('#1stCol').addClass('col-md-4');

            //     $('#2ndCol').removeClass('col-md-10');
            //     $('#2ndCol').addClass('col-md-4');

            //     $('#3rdCol').removeClass('col-md-1');
            //     $('#3rdCol').addClass('col-md-4');
            // });

            @if (old('name') || !empty($id))
                //trigger tab-report
                activeTab('pills-register');
                $('#tab-register').trigger('click');
            @endif


          
            $("#birthday").datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-80y:c+nn',
                maxDate: '-1d'
            });

            ///////////////////////////////////
            // place dropdown
            ///////////////////////////////////
            var p1 = $("#place").select2({
                width: '100%', // need to override the changed default
                closeOnSelect: true,
                placeholder: "Select Place",
            });

        });
    </script>

<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'en,hi,ta,te,kn,ml,gu,pa,bn,mr,or,as,ne,sd,ur', // English, Hindi, and regional Indian languages
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
            autoDisplay: false,
            multilanguagePage: true
        }, 'google_translate_element');
    }
</script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>

</html>
