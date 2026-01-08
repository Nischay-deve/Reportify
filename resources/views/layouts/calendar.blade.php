<!doctype html>
<html lang="en">

<head>
    <base href="{{ route('calendar.index', $website_slug) }}">
    <meta charset="utf-8" />
    <title>@yield('title') - {{config('app.name')}}</title>
    
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> --}}
    
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

    {{-- <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css"> --}}

    <link rel="stylesheet" href="{{ asset('libs/jquery-ui-1.13.2.custom/jquery-ui.css') }}">
    

    @stack('styles')

    @if (config('app.color_scheme') == 'pink')
        <style>        
            .mm-active .active {
                color: #FF5500 !important;
            }

            .mm-active>a {
                color: #FF5500 !important;
                border-color: #FF5500 !important;
            }

            .mm-active>a i {
                color: #FF5500 !important;
            }

            .mm-active>i {
                color: #FF5500 !important;
            }

            .mm-active {
                color: #FF5500 !important;
            }

            #sidebar-menu ul li ul.sub-menu li a:hover {
                color: #FF5500;
            }

            #sidebar-menu ul li a:hover {
                color: #FF5500;
            }

            #sidebar-menu ul li a:hover i {
                color: #FF5500;
            }

            .mm-active .active i {
                color: #FF5500 !important;
            }

            .btn-pink:hover {
                color: #fff;
                background-color: #FF5500 !important;
                border-color: #FF5500 !important;
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
                background-color: #FF5500 !important;
                border-color: #FF5500 !important;
            }


            .btn-check:focus+.btn-pink,
            .btn-pink:focus {
                color: #fff;
                background-color: #FF5500 !important;
                border-color: #FF5500 !important;
                -webkit-box-shadow: 0 0 0 0.15rem rgb(255 126 170 / 50%);
                box-shadow: 0 0 0 0.15rem rgb(255 126 170 / 50%);
            }

            .btn-check:active+.btn-pink,
            .btn-check:checked+.btn-pink,
            .btn-pink.active,
            .btn-pink:active,
            .show>.btn-pink.dropdown-toggle {
                color: #fff;
                background-color: #FF5500 !important;
                border-color: #FF5500 !important;
            }


            .search-box button.search-btn {
                background: url(./images/sprite.png) no-repeat scroll 0px -83px #FF5500;
                height: 37px;
                width: 45px;
                -webkit-border-radius: 0 5px 5px 0;
                -moz-border-radius: 0 5px 5px 0;
                -o-border-radius: 0 5px 5px 0;
                border-radius: 0 5px 5px 0;
            }
            .search-box button.search-btn {
                background: url(./images/sprite.png) no-repeat scroll -4px -89px #FF5500;
                border: none;
                height: 37px;
                width: 42px;
                position: absolute;
                right: -1px;
                top: 0;
            }
            .search-box button.reset-btn {
                background-color: #6c757d;
                border: none;
                height: 22px;
                width: 22px;
                position: absolute;
                right: 50px;
                top: 8px;
                color: white;
                text-align: center;
                border-radius: 50px !important;
            }

        </style>
    @else
        <style>    
            .search-box button.search-btn {
                background: url(../images/sprite.png) no-repeat scroll 0px -83px #44a2d2;
                height: 45px;
                width: 45px;
                -webkit-border-radius: 0 5px 5px 0;
                -moz-border-radius: 0 5px 5px 0;
                -o-border-radius: 0 5px 5px 0;
                border-radius: 0 5px 5px 0;
            }
            .search-box button.search-btn {
                background: url(../images/sprite.png) no-repeat scroll -4px -89px #44a2d2;
                border: none;
                height: 42px;
                width: 42px;
                position: absolute;
                right: -1px;
                top: 0;
            }
            .search-box button.reset-btn {
                background-color: #6c757d;
                border: none;
                height: 22px;
                width: 22px;
                position: absolute;
                right: 50px;
                top: 8px;
                color: white;
                text-align: center;
                border-radius: 50px !important;
            }
        </style>    
    @endif

    <script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property={{config('app.share_this_property')}}&product=sop' async='async'></script>
</head>

<body data-topbar="dark">

    @include('partials.header')

    <!-- Begin page -->
    <div id="layout-wrapper">

        <div class="row">
           
            <div class="col-md-12">
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

                    @yield('content')
                @endif                
            </div>
           
        </div>                    

        @include('partials.footer')

    </div>
   
    

    <!-- JAVASCRIPT -->

    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>


    <script src="{{ asset('libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script src="{{ asset('js/app.js') }}"></script>

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

    <script>
    function matchCustomBase(params, data) {
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

    
    function zeroPopup(issueName, issueDate){
        Swal.fire({
            icon: 'warning',
            title: 'Oops',
            html: 'No reports added for '+issueName+' for <br>'+issueDate,
            confirmButtonText: 'Ok',
        }).then((result) => {
            if (result.isConfirmed) {

            } else if (result.isDenied) {

            }
        })            
    }

        $(document).ready(function() {


            $("#loadCenterButton").click(function(){
                /* $("#selectedDateContainer").show(); */
                $('#loadCenterModal').modal('show');
            });

            var cw1 = $("#center_website").select2({
            // width: '100%', // need to override the changed default
            closeOnSelect: false,
            placeholder: "Select Centers",
            tags: true,
            tokenSeparators: [','],
            // minimumInputLength: 1,
            matcher: matchCustomBase,
            language: {
                inputTooShort: function(args) {
                    return "";
                }
            },
        });     

        });

        function reload(){
            $('#search-box').val('');
            $('#search-form').submit();            
        }

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

    @stack('scripts')
    <!-- END PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

</body>

</html>
