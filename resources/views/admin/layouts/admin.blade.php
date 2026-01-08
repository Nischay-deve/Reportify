<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>ThisDateThatYear</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <link href="{{ asset('libs/metrojs/release/MetroJs.Full/MetroJs.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
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

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/select2.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">

    @stack('styles')

    @if (config('app.color_scheme') == 'pink')
        <style>
            /* .form-check-input {
                    background-color: #FF6B1F !important;
                    border-color: #FF6B1F !important;
                }

                .form-check-input:checked {
                    background-color: #FF6B1F !important;
                    border-color: #FF6B1F !important;
                } */

            .mm-active .active {
                color: #FF6B1F !important;
            }

            .mm-active>a {
                color: #FF6B1F !important;
                border-color: #FF6B1F !important;
            }

            .mm-active>a i {
                color: #FF6B1F !important;
            }

            .mm-active>i {
                color: #FF6B1F !important;
            }

            .mm-active {
                color: #FF6B1F !important;
            }

            #sidebar-menu ul li ul.sub-menu li a:hover {
                color: #FF6B1F;
            }

            #sidebar-menu ul li a:hover {
                color: #FF6B1F;
            }

            #sidebar-menu ul li a:hover i {
                color: #FF6B1F;
            }

            .mm-active .active i {
                color: #FF6B1F !important;
            }

            .btn-pink:hover {
                color: #fff;
                background-color: #FF6B1F !important;
                border-color: #FF6B1F !important;
            }

            .btn-danger {
                color: #fff;
                background-color: #f1556c;
                border-color: #f1556c;
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
        </style>
    @endif

</head>

<body data-topbar="dark">

    @include('admin.partials.header')

    <!-- Begin page -->
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
                @if(Route::currentRouteName() == "report.public")
                    <div class="row">
                        <div class="col-md-12" style="margin-top:50px;">
                            <div class="card">
                                <div class="card-header bg-white text-white text-center text-bold font-size-16">&nbsp;</div>
                                <div class="card-body">
                                    @yield('content')
                                </div>
                            </div>
                        </div>
                    </div>                
                @else
                    <!-- Help Content -->
                    @include ('admin.partials.help')

                    @yield('content')
                @endif                
            </div>
            <div class="col-md-1">
            </div>
        </div>                    

  
        <!-- end main content-->

        @include('admin.partials.footer')

    </div>
   
    

    <!-- JAVASCRIPT -->

    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>


    <script src="{{ asset('libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script src="{{ asset('js/app.js') }}"></script>

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

    <script>
        function confirmDelete(formId) {
            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'This item will be marked as deleted!',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, I am sure!',
                cancelButtonText: "No, cancel it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#' + formId).submit();
                } else if (result.isDenied) {
                    return false;
                }
            })

        }


        function confirmLoginAsUser(formId) {
            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'You will be logged out as logged in as this user!',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, I am sure!',
                cancelButtonText: "No, cancel it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#' + formId).submit();
                } else if (result.isDenied) {
                    return false;
                }
            })

        }

        function confirmRestore(formId) {
            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'This item will be restored!',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, I am sure!',
                cancelButtonText: "No, cancel it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#' + formId).submit();
                } else if (result.isDenied) {
                    return false;
                }
            })

        }
    </script>

    @stack('scripts')
    <!-- END PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

    <script>       
        function showDialog(title, msg, ic = 'warning', confirm = false, callback = null) {
            event.preventDefault();
            var dataTitle = title;
            Swal.fire({
                title: dataTitle,
                text: msg,
                icon: ic,
                showCancelButton: true,
                showConfirmButton: confirm,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                cancelButtonText: "Close"
            }).then(
                function() {
                    if (callback) {
                        callback()
                    }
                },
                function() {
                    return false;
                });
        }
    </script>

    <!-- Google Translate Script -->
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
